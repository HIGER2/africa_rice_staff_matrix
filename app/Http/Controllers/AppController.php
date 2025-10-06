<?php

namespace App\Http\Controllers;

use App\Mail\TimeAllocationUpdatedMail;
use App\Models\Employee;
use App\Models\monthlyTotal;
use App\Services\AllocationService;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AppController extends Controller
{

    function __construct(protected AllocationService $allocationService) {}

    public function index(): Response
    {

        $employees = DB::table('employees')
            ->leftJoin('employees as supervisors', 'employees.supervisorId', '=', 'supervisors.employeeId')
            ->select(
                'employees.employeeId',
                'employees.email',
                'employees.supervisorId',
                'employees.matricule as resno',
                'employees.firstName as name',
                DB::raw("CONCAT(employees.firstName, ' ', employees.lastName) as employee_name"),
                'employees.lastName as lastName',
                'supervisors.matricule as supervisors_matricule',
                DB::raw("CONCAT(supervisors.firstName, ' ', supervisors.lastName) as supervisor_name"),
                'employees.bgLevel as grade_level',
                'employees.grade',
                'employees.phone2 as division',
                'employees.jobTitle as position',
                'employees.organization',
                'employees.country_of_residence',
                'employees.base_station',
                'employees.unit_program'
            )
            ->where('employees.firstName', '!=', 'admin')
            ->get();

        return Inertia::render('Home', [
            'staff' => $employees
        ]);
    }

    public function updateEmployee(Request $request)
    {

        // Récupérer l'employé via employeeId ou autre identifiant unique
        $employee = Employee::where('employeeId', $request['employeeId'])->first();

        if (!$employee) {
            return response()->json(['message' => 'Employé non trouvé'], 404);
        }

        // Récupérer le superviseur correspondant au matricule
        $supervisor = Employee::where('matricule', $request['supervisors_matricule'])->first();

        if (!$supervisor) {
            return response()->json(['message' => 'Superviseur non trouvé'], 404);
        }

        // Vérifier si le supervisorId de l'employé correspond à l'ID du superviseur
        if ($employee->supervisorId != $supervisor->employeeId) {
            $employee->supervisorId = $supervisor->employeeId;
        }

        // Mettre à jour les informations de l'employé avec les données reçues
        $employee->firstName = $request['name'] ?? $employee->name;
        $employee->lastName = $request['lastName'] ?? $employee->lastName;
        $employee->email = $request['email'] ?? $employee->email;
        $employee->grade = $request['grade'] ?? $employee->grade;
        $employee->bgLevel = $request['grade_level'] ?? $employee->grade_level;
        $employee->jobTitle = $request['position'] ?? $employee->position;
        $employee->phone2 = $request['division'] ?? $employee->division;
        $employee->organization = $request['organization'] ?? $employee->organization;
        $employee->unit_program = $request['unit_program'] ?? $employee->unit_program;
        $employee->country_of_residence = $request['country_of_residence'] ?? $employee->country_of_residence;
        $employee->base_station = $request['base_station'] ?? $employee->base_station;

        // Sauvegarder les modifications
        $employee->save();

        return response()->json([
            'message' => 'Employé mis à jour avec succès',
            'employee' => $employee
        ], 200);
    }

    public function importAllocation()
    {
        $employees = DB::table('employees')
            ->where('employees.firstName', '!=', 'admin')
            ->leftJoin('employees as supervisors', 'employees.supervisorId', '=', 'supervisors.employeeId')
            ->leftJoin('time_allocations', 'employees.employeeId', '=', 'time_allocations.employeeId')
            ->select(
                // 'employees.employeeId',
                // 'employees.email',
                'employees.matricule as resno',
                DB::raw("CONCAT(employees.firstName, ' ', employees.lastName) as name"),
                'employees.bgLevel as grade level',
                'employees.grade',
                'employees.phone2 as division',
                'employees.jobTitle as position',
                'employees.organization',
                'employees.country_of_residence as country of residence',
                'employees.base_station as base station',
                'employees.unit_program as unit program',
                DB::raw("CONCAT(supervisors.firstName, ' ', supervisors.lastName) as supervisor_name"),
                'time_allocations.agreement',
                'time_allocations.bus',
                'time_allocations.jan',
                'time_allocations.feb',
                'time_allocations.mar',
                'time_allocations.apr',
                'time_allocations.may',
                'time_allocations.jun',
                'time_allocations.jul',
                'time_allocations.aug',
                'time_allocations.sep',
                'time_allocations.oct',
                'time_allocations.nov',
                'time_allocations.dec',
                'time_allocations.total'
            )
            ->get();


        return response()->json(['data' => $employees]);
    }

    public function addAllocation(Request $request)
    {
        $allocations = $request->timeAllocations;
        $employeeId = $request->employeeId;
        $monthlyTotal = $request->monthlyTimeTotal;
        $year = Carbon::now()->year;

        // return response()->json(['message' => $employeeId]);

        // $employee = Employee::where('employeeId', $employeeId)->first();
        $employee = Employee::where('employeeId', $employeeId)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        foreach ($allocations as $value) {
            $value['year'] = date('Y');
            $value['employeeId'] = $employeeId;
            $employee->timeAllocations()->updateOrCreate(
                [
                    'id' => $value['id'] ?? null,
                    'employeeId' => $employeeId,
                    'year' => $year
                ],
                $value
            );
        }

        // unset($monthlyTotal['id']);
        // monthlyTotal::updateOrCreate(
        //     [
        //     // 'id' => $monthlyTotal['id'],
        //     'employeeId' => $employeeId,
        //     'year'=>$year
        //     ],
        //     $monthlyTotal
        // );

        // $timeAllocation = $employee->timeAllocations()->where('year', date('Y'))->get();
        // $employee = Employee::select(
        //         'employeeId',
        //         'email',
        //         'supervisorId', 
        //         'matricule as resno',
        //         'firstName as name',
        //         'lastName as lastName',
        //         'bgLevel as grade_level',
        //         'grade',
        //         'phone2 as division',
        //         'jobTitle as position',
        //         'organization',
        //         'country_of_residence',
        //         'base_station',
        //         'unit_program'
        //     )
        //     ->with([
        //         'timeAllocations' => function ($query) {
        //             $query->select(
        //             'id',
        //             'employeeId',
        //             'agreement',
        //             'bus',
        //             'jan',
        //             'feb',
        //             'mar',
        //             'apr',
        //             'may',
        //             'jun',
        //             'jul',
        //             'aug',
        //             'sep',
        //             'oct',
        //             'nov',
        //             'dec',
        //             'date',
        //             'total'
        //         )->orderBy('created_at', 'desc');

        //     },
        //     'monthlyTotal' => function ($query) {
        //             $query->select(
        //                 'id',
        //                 'employeeId', // pour la relation timeAllocations
        //                 'jan',
        //                 'feb',
        //                 'mar',
        //                 'apr',
        //                 'may',
        //                 'jun',
        //                 'jul',
        //                 'aug',
        //                 'sep',
        //                 'oct',
        //                 'nov',
        //                 'dec',
        //                 'date',
        //                 'total'
        //             )
        //             ->whereYear('date', Carbon::now()->year) ;
        //         }
        //     ])
        //     ->where('employeeId', $employeeId)
        //     ->first();

        // $timeAllocations = $employee->timeAllocations->map(function ($allocation) {
        //     return [
        //         // 'Year' => $allocation->year ?? 'N/A',
        //         'Agreement' => $allocation->agreement,
        //         'Bus' => $allocation->bus,
        //         'Jan' => $allocation->jan,
        //         'Feb' => $allocation->feb,
        //         'Mar' => $allocation->mar,
        //         'Apr' => $allocation->apr,
        //         'May' => $allocation->may,
        //         'Jun' => $allocation->jun,
        //         'Jul' => $allocation->jul,
        //         'Aug' => $allocation->aug,
        //         'Sep' => $allocation->sep,
        //         'Oct' => $allocation->oct,
        //         'Nov' => $allocation->nov,
        //         'Dec' => $allocation->dec,
        //         'Total' => $allocation->total,
        //         // 'Date' => $allocation->date,
        //     ];
        // })->toArray();

        //     $supervisorEmail = $employee->supervisor->email ?? null;
        //     $mail = Mail::to($employee->email);
        //     if ($supervisorEmail) {
        //         $mail->cc($supervisorEmail);
        //     }

        // $mail->send(new TimeAllocationUpdatedMail($employee, $timeAllocations));

        return response()->json(['message' => 'Allocations saved successfully', 'data' => $employee]);
    }

    public function deleteTimeAllocation(Request $request)
    {
        $id = $request->id;
        $employeeId = $request->employeeId;

        $employee = Employee::where('employeeId', $employeeId)->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        $timeAllocation = $employee->timeAllocations()->where('id', $id)->first();

        if (!$timeAllocation) {
            return response()->json(['message' => 'Time allocation not found'], 404);
        }
        // Supprimer la timeAllocation
        $timeAllocation->delete();

        $employee = Employee::select(
            'employeeId',
            'email',
            'supervisorId',
            'matricule as resno',
            'firstName as name',
            'lastName as lastName',
            'bgLevel as grade_level',
            'grade',
            'phone2 as division',
            'jobTitle as position',
            'organization',
            'country_of_residence',
            'base_station',
            'unit_program'
        )
            ->with([
                'timeAllocations' => function ($query) {
                    $query->select(
                        'id',
                        'employeeId',
                        'agreement',
                        'bus',
                        'jan',
                        'feb',
                        'mar',
                        'apr',
                        'may',
                        'jun',
                        'jul',
                        'aug',
                        'sep',
                        'oct',
                        'nov',
                        'dec',
                        'date',
                        'total'
                    )->orderBy('created_at', 'desc');
                },
                'monthlyTotal' => function ($query) {
                    $query->select(
                        'id',
                        'employeeId', // pour la relation timeAllocations
                        'jan',
                        'feb',
                        'mar',
                        'apr',
                        'may',
                        'jun',
                        'jul',
                        'aug',
                        'sep',
                        'oct',
                        'nov',
                        'dec',
                        'date',
                        'total'
                    )
                        ->whereYear('date', Carbon::now()->year);
                }
            ])
            ->where('employeeId', $employeeId)
            ->first();

        return response()->json(['message' => 'Allocations saved successfully', 'data' => $employee]);
    }
    public function sendMail(Request $request)
    {
        try {
            $employeeId = $request->employeeId;
            // $timeAllocation = $employee->timeAllocations()->where('year', date('Y'))->get();
            $employee = Employee::select(
                'employeeId',
                'email',
                'supervisorId',
                'matricule as resno',
                'firstName as name',
                'lastName as lastName',
                'bgLevel as grade_level',
                'grade',
                'phone2 as division',
                'jobTitle as position'
            )
                ->with([
                    'timeAllocations' => function ($query) {
                        $query->select(
                            'id',
                            'employeeId',
                            'agreement',
                            'bus',
                            'jan',
                            'feb',
                            'mar',
                            'apr',
                            'may',
                            'jun',
                            'jul',
                            'aug',
                            'sep',
                            'oct',
                            'nov',
                            'dec',
                            'date',
                            'total'
                        )->orderBy('created_at', 'desc');
                    },
                    'monthlyTotal' => function ($query) {
                        $query->select(
                            'id',
                            'employeeId', // pour la relation timeAllocations
                            'jan',
                            'feb',
                            'mar',
                            'apr',
                            'may',
                            'jun',
                            'jul',
                            'aug',
                            'sep',
                            'oct',
                            'nov',
                            'dec',
                            'date',
                            'total'
                        )
                            ->whereYear('date', Carbon::now()->year);
                    }
                ])
                ->where('employeeId', $employeeId)
                ->first();
            if ($employee->timeAllocations->isEmpty()) {
                return response()->json(['message' => 'Email not send allocations empty']);
            }
            $timeAllocations = $employee->timeAllocations->map(function ($allocation) {
                return [
                    // 'Year' => $allocation->year ?? 'N/A',
                    'Agreement' => $allocation->agreement,
                    'Bus' => $allocation->bus,
                    'Jan' => $allocation->jan,
                    'Feb' => $allocation->feb,
                    'Mar' => $allocation->mar,
                    'Apr' => $allocation->apr,
                    'May' => $allocation->may,
                    'Jun' => $allocation->jun,
                    'Jul' => $allocation->jul,
                    'Aug' => $allocation->aug,
                    'Sep' => $allocation->sep,
                    'Oct' => $allocation->oct,
                    'Nov' => $allocation->nov,
                    'Dec' => $allocation->dec,
                    'Total' => $allocation->total,
                    // 'Date' => $allocation->date,
                ];
            })->toArray();
            $supervisorEmail = $employee->supervisor->email ?? null;
            $mail = Mail::to($employee->email);
            if ($supervisorEmail) {
                $mail->cc($supervisorEmail);
            }

            if (empty($employee->email) || !filter_var($employee->email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => 'Invalid or missing email',
                ], 422);
            }
            $mail->send(new TimeAllocationUpdatedMail($employee, $timeAllocations));
            return response()->json(['message' => 'Email sent successfully', 'data' => $employee->timeAllocations]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error send allocations',
            ], 500);
        }
    }

    public function StaffTimeAllocations($id)
    {
        $employee = Employee::select(
            'employeeId',
            'email',
            'supervisorId',
            'matricule as resno',
            'firstName as name',
            'lastName as lastName',
            'bgLevel as grade_level',
            'grade',
            'phone2 as division',
            'jobTitle as position',
            'organization',
            'country_of_residence',
            'base_station',
            'unit_program'
        )
            ->with(
                [
                    'timeAllocations' => function ($query) {
                        $query->select(
                            'id',
                            'employeeId',
                            'agreement',
                            'bus',
                            'jan',
                            'feb',
                            'mar',
                            'apr',
                            'may',
                            'jun',
                            'jul',
                            'aug',
                            'sep',
                            'oct',
                            'nov',
                            'dec',
                            'date',
                            'total'
                        )->orderBy('created_at', 'desc');
                    },
                    // 'monthlyTotal' => function ($query) {
                    //         $query->select(
                    //             'id',
                    //             'employeeId', // pour la relation timeAllocations
                    //             'jan',
                    //             'feb',
                    //             'mar',
                    //             'apr',
                    //             'may',
                    //             'jun',
                    //             'jul',
                    //             'aug',
                    //             'sep',
                    //             'oct',
                    //             'nov',
                    //             'dec',
                    //             'date',
                    //             'total'
                    //         )
                    //         ->whereYear('date', Carbon::now()->year) ;
                    // }
                ]
            )
            ->where('employeeId', $id)
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json(['data' => $employee]);
    }


    public function uploadAllocation(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048', // max 2MB
        ]);

        return $this->allocationService->uploadAllocation($request);
    }
}
