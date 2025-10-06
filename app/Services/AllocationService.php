<?php

namespace App\Services;

use App\Helpers\helpers;
use App\Models\Employee;
use App\Models\TimeAllocation;

class AllocationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public  function uploadAllocation($request)
    {
        try {
            $uploadedFile = $request->file('file');
            $this->SaveAllocation($uploadedFile->getRealPath());
            return response()->json([
                'message' => 'Fichier uploadé avec succès',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Une erreur s'est produite",
                "error" => $th->getMessage()
            ], 500);
        }
    }

    public  function timeAllocation($data)
    {
        $employeeDataSlice = array_slice($data, 0, 11);
        $timeAllocation = array_slice($data, 12);

        $parts = explode(' ', $employeeDataSlice[1]);
        $lastName = $parts[0]; // Nom de famille
        $firstName = isset($parts[1]) ? trim($parts[1]) : ''; // Prénom
        $employeeData = [
            "matricule" => $employeeDataSlice[0],
            "firstName" => $firstName,
            "lastName" => $lastName,
            "bgLevel" => $employeeDataSlice[2],
            "grade" => $employeeDataSlice[3],
            "division" => $employeeDataSlice[4],
            "phone2" => $employeeDataSlice[4],
            "jobTitle" => $employeeDataSlice[5],
            "organization" => $employeeDataSlice[6],
            "country_of_residence" => $employeeDataSlice[7],
            "base_station" => $employeeDataSlice[8],
            "unit_program" => $employeeDataSlice[9],
            "password" => "",
            "email" => $employeeDataSlice[0]
        ];

        $supervisorMatricule = $employeeDataSlice[10];

        for ($i = 14; $i <= 26; $i++) {
            // Si la valeur est une chaîne, on la convertit en int
            if (isset($data[$i])) {
                $data[$i] = (int) $data[$i];
            }
        }
        $employee = Employee::where('matricule', $employeeData['matricule'])->first();
        $supervisor = Employee::where('matricule', $supervisorMatricule)->first();

        if ($supervisor) {
            $employeeData['supervisorId'] = $supervisor->employeeId;
        }
        if (!$employee) {
            // Si n’existe pas, créer
            $employee = Employee::create($employeeData);
        } else {
            // S’il existe, mettre à jour seulement certains champs (exemple : 'name' et 'email')
            $employee->update([
                // "matricule"=> $employeeData[0],
                "firstName" => $firstName,
                "lastName" => $lastName,
                // "password"=>"" ,
                // "jobTitle"=> $employeeData[2] ,
                "bgLevel" => $employeeData['bgLevel'],
                "grade" => $employeeData['grade'],
                "organization" => $employeeData['organization'],
                "country_of_residence" => $employeeData['country_of_residence'],
                "base_station" => $employeeData['base_station'],
                "division" => $employeeData['division'],
                "phone2" => $employeeData['division'],
                "unit_program" => $employeeData['unit_program'],
                "jobTitle" => $employeeData['jobTitle'],
                // "email"=>$employeeData[0]
            ]);
        }

        if ($employee->wasRecentlyCreated) {
            $new[] = $employee;
        }

        if ($employee) {
            $agreement = $data[11] ?? null;
            $bus = $data[12] ?? null;
            $payload = [
                'employeeId' => $employee->employeeId,
                'year' => date('Y'),
                'agreement' => $agreement,
                'bus' => $bus,
                'jan' => helpers::normalizeNumber($data[13]),
                'feb' => helpers::normalizeNumber($data[14]),
                'mar' => helpers::normalizeNumber($data[15]),
                'apr' => helpers::normalizeNumber($data[16]),
                'may' => helpers::normalizeNumber($data[17]),
                'jun' => helpers::normalizeNumber($data[18]),
                'jul' => helpers::normalizeNumber($data[19]),
                'aug' => helpers::normalizeNumber($data[20]),
                'sep' => helpers::normalizeNumber($data[21]),
                'oct' => helpers::normalizeNumber($data[22]),
                'nov' => helpers::normalizeNumber($data[23]),
                'dec' => helpers::normalizeNumber($data[24]),
                'total' => helpers::normalizeNumber($data[25]),
            ];
            // ✅ Vérifie si ce TimeAllocation existe déjà
            $alreadyExists = $employee->timeAllocations()->updateOrCreate(
                [
                    'agreement' => $agreement,
                    'bus' => $bus,
                    'year' => date('Y'),
                ],
                $payload
            );

            // ->where('agreement', $agreement)
            // ->where('bus', $bus)
            // ->where('year', date('Y'))
            // ->exists();

            // if (!$alreadyExists) {
            //     $payload = [
            //         'employeeId' => $employee->employeeId,
            //         'year' => date('Y'),
            //         // 'agreement' => $agreement,
            //         // 'bus' => $bus,
            //         'jan' => helpers::normalizeNumber($data[13]),
            //         'feb' => helpers::normalizeNumber($data[14]),
            //         'mar' => helpers::normalizeNumber($data[15]),
            //         'apr' => helpers::normalizeNumber($data[16]),
            //         'may' => helpers::normalizeNumber($data[17]),
            //         'jun' => helpers::normalizeNumber($data[18]),
            //         'jul' => helpers::normalizeNumber($data[19]),
            //         'aug' => helpers::normalizeNumber($data[20]),
            //         'sep' => helpers::normalizeNumber($data[21]),
            //         'oct' => helpers::normalizeNumber($data[22]),
            //         'nov' => helpers::normalizeNumber($data[23]),
            //         'dec' => helpers::normalizeNumber($data[24]),
            //         'total' => helpers::normalizeNumber($data[25]),
            //     ];
            //     // TimeAllocation::create($payload);

            //     TimeAllocation::updateOrcreate([
            //         [
            //             'agreement' => $agreement,
            //             'bus' => $bus,
            //         ],
            //         $payload
            //     ]);
            // }
        }
    }

    public function SaveAllocation($filePath)
    {
        $data = helpers::extractDataXlsx($filePath);

        foreach ($data as $key => $row) {
            if ($key == 0) continue;
            self::timeAllocation($row);
        }
    }
}
