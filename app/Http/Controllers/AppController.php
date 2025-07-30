<?php

namespace App\Http\Controllers;

use App\Mail\TimeAllocationUpdatedMail;
use App\Models\Employee;
use App\Models\monthlyTotal;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppController extends Controller
{
    
    public function index(): Response
    {

            $employees = Employee::with([
                'timeAllocations' => function ($query) {
                    $query->select(
                        'id',
                        'employeeId', // pour la relation timeAllocations
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
                    )
                    ->whereYear('date', Carbon::now()->year) // 👈 filtre année actuelle
                    ->orderBy('created_at', 'desc');
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
                    ->whereYear('date', Carbon::now()->year) // 👈 filtre année actuelle
                    ->orderBy('created_at', 'desc');
                },
                'supervisor' => function ($query) {
                    $query->select('employeeId', 'firstName', 'lastName');
                }
            ])
            ->select(
                'employeeId',
                'supervisorId', // <<< AJOUTÉ : clé étrangère indispensable
                'matricule as resno',
                'firstName as name',
                'lastName as lastName',
                'bgLevel as grade_level',
                'grade',
                'phone2 as division',
                'jobTitle as position'
            )
            ->where('firstName', '!=', 'admin')
            // ->take(3)
            ->get();
        return Inertia::render('Home', [
            'staff' =>$employees
        ]);
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
                ['id' => $value['id'] ?? null, 
                'employeeId' => $employeeId,
                'year'=>$year
                ],
                $value
            );
        }

        // unset($monthlyTotal['id']);
        monthlyTotal::updateOrCreate(
            [
            // 'id' => $monthlyTotal['id'],
            'employeeId' => $employeeId,
            'year'=>$year
            ],
            $monthlyTotal
        );
        
        $timeAllocation = $employee->timeAllocations()->where('year', date('Y'))->get();
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
                    ->whereYear('date', Carbon::now()->year) ;
                }
            ])
            ->where('employeeId', $employeeId)
            ->first();

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
        
        return response()->json(['message' => 'Allocations saved successfully','data'=>$employee]);
    }

    public function sendMail(Request $request)
    {
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
                    ->whereYear('date', Carbon::now()->year) ;
                }
            ])
            ->where('employeeId', $employeeId)
            ->first();

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
            $mail->send(new TimeAllocationUpdatedMail($employee, $timeAllocations));
        return response()->json(['message' => 'Allocations saved successfully','data'=>$employee]);
    }

}
