<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\TimeAllocation;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TimeAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('file4.xlsx');
        // $rows = Excel::toArray([], $filePath);
        // $sheet = $rows[0];       // la première feuille
        // $header = $sheet[0];  
        // $data = array_slice($sheet, 1); // lignes suivantes = données
        // print_r($data[180]);
        // var_dump($header[26]);
            //  echo "Données:\n";
            // foreach ($data as $row) {
            //     echo $row[180];
            // }
        // $spreadsheet = IOFactory::load($path);
        // $worksheet = $spreadsheet->getActiveSheet();
        // $rows = $worksheet->toArray();

        // foreach ($rows as $index => $row) {
        //     if ($index === 0) continue; // skip header
        //     // Adapter selon la position des colonnes
        //     $resno = $row[0]; // supposons que le resno est dans la première colonne

        //     if (!$resno) continue;

        //     $employee = Employee::where('matricule', $resno)->first(); // adapter selon ton champ

        //     if ($employee) {
        //         $months = array_slice($row, 5, 12); // Jan à Dec

        //         $months = array_map(function ($val) {
        //             $val = str_replace('%', '', $val); // enlever %
        //             return is_numeric($val) ? intval($val) : 0;
        //         }, $months);

        //         TimeAllocation::create([
        //             'employeeId' => $employee->id,
        //             'year' => 2025,
        //             'agreement' => $row[2] ?? null,
        //             // 'date' => $row[3] ?? null,
        //             'bus' => $row[4] ?? null,
        //             'jan' => $months[0] ?? 0,
        //             'feb' => $months[1] ?? 0,
        //             'mar' => $months[2] ?? 0,
        //             'apr' => $months[3] ?? 0,
        //             'may' => $months[4] ?? 0,
        //             'jun' => $months[5] ?? 0,
        //             'jul' => $months[6] ?? 0,
        //             'aug' => $months[7] ?? 0,
        //             'sep' => $months[8] ?? 0,
        //             'oct' => $months[9] ?? 0,
        //             'nov' => $months[10] ?? 0,
        //             'dec' => $months[11] ?? 0,
        //             'total' => array_sum($months)
        //         ]);
        //     }
        // }

        $data = [];
        $new =[];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowData = [];
            foreach ($cellIterator as $cell) {
                    $value = $cell->getFormattedValue();
                    // Enlever le % s'il est présent
                    // $value = str_replace('%', '', $value);
                    // //   // Convertir en entier
                    // $value = (int) $value;  
                    $rowData[] = $value;
            }
            $data[] = $rowData;
        }
                function normalizeNumber($value): int|float {
                    if (is_null($value) || $value === '' || !is_numeric($value)) {
                        return 0;
                    }
                    return $value; 
                }
        
        function timeAllocation($data){
                $employeeData = array_slice($data,1,11);
                $timeAllocation = array_slice($data,12);

                $parts = explode(',', $employeeData[1]);
                $lastName = $parts[0]; // Nom de famille
                $firstName = isset($parts[1]) ? trim($parts[1]) : ''; // Prénom
                $employeeData=[
                    "matricule"=> $employeeData[0],
                    "firstName"=>$firstName ,
                    "lastName"=> $lastName ,
                    "password"=>"" ,
                    "jobTitle"=> $employeeData[2] ,
                    "bgLevel"=> $employeeData[3] ,
                    "grade"=> $employeeData[4] ,
                    "organization"=> $employeeData[5] ,
                    "country_of_residence"=> $employeeData[6] ,
                    "base_station"=> $employeeData[7] ,
                    "division"=> $employeeData[8] ,
                    "unit_program"=> $employeeData[9] ,
                    "email"=>$employeeData[0]
                ];

                // $resno = $data[1];
                // if ($resno !== 'A10552') {
                //         return;
                //     }
                for ($i = 14; $i <= 26; $i++) {
                    // Si la valeur est une chaîne, on la convertit en int
                    if (isset($data[$i])) {
                        $data[$i] = (int) $data[$i];
                    }
                }
                $employee = Employee::where('matricule', $employeeData['matricule'])->first();

                if (!$employee) {
                    // Si n’existe pas, créer
                    $employee = Employee::create($employeeData);
                } else {
                    // S’il existe, mettre à jour seulement certains champs (exemple : 'name' et 'email')
                    $employee->update([
                        // "matricule"=> $employeeData[0],
                        // "firstName"=>$firstName ,
                        // "lastName"=> $lastName ,
                        // "password"=>"" ,
                        // "jobTitle"=> $employeeData[2] ,
                        "bgLevel"=> $employeeData['bgLevel'] ,
                        "grade"=> $employeeData['grade'] ,
                        "organization"=> $employeeData['organization'] ,
                        "country_of_residence"=> $employeeData['country_of_residence'] ,
                        "base_station"=> $employeeData['base_station'] ,
                        "division"=> $employeeData['division'] ,
                        "phone2"=> $employeeData['division'] ,
                        "unit_program"=> $employeeData['unit_program'] ,
                        // "email"=>$employeeData[0]
                    ]);
                }

                // $employee = Employee::firstOrCreate(
                //     ['matricule' => $employeeData['matricule']],
                //     $employeeData
                // );
                // $employee = Employee::updateOrCreate(
                //     ['matricule'=>$employeeData['matricule']],
                //     $employeeData
                // ); 
                // email
                // lastName
                // password
                if ($employee->wasRecentlyCreated) {
                    $new[] = $employee;
                }

                if ($employee) {
                    $agreement = $data[12] ?? null;
                    $bus = $data[13] ?? null;
                    // ✅ Vérifie si ce TimeAllocation existe déjà
                    $alreadyExists = $employee->timeAllocations()
                        ->where('agreement', $agreement)
                        ->where('bus', $bus)
                        ->where('year', date('Y'))
                        ->exists();

                      
                        
                        if (!$alreadyExists) {

                        $payload = [
                            'employeeId' => $employee->employeeId,
                            'year' => date('Y'),
                            'agreement' => $agreement,
                            'bus' => $bus,
                            'jan' => normalizeNumber($data[14]),
                            'feb' => normalizeNumber($data[15]),
                            'mar' => normalizeNumber($data[16]),
                            'apr' =>normalizeNumber($data[17]),
                            'may' =>normalizeNumber($data[18]),
                            'jun' =>normalizeNumber($data[19]),
                            'jul' =>normalizeNumber($data[20]),
                            'aug' => normalizeNumber($data[21]),
                            'sep' => normalizeNumber($data[22]),
                            'oct' => normalizeNumber($data[23]),
                            'nov' => normalizeNumber($data[24]),
                            'dec' => normalizeNumber($data[25]),
                            'total' =>normalizeNumber($data[26]),
                        ];
                        TimeAllocation::create($payload);
                    }
                }
        }

        foreach ($data as $key=> $row) {
            if ($key == 0 || $key==1) continue;
            timeAllocation($row);
        //   var_dump($key);
        // break;
        }

        // var_dump($data[11]);
        // timeAllocation($data[11]);

        // function monthlyTota(){
        //     $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        //     function calculateMonthlyTotals(array $data, array $months): array {
        //         $result = array_fill_keys($months, 0);
        //         $grandTotal = 0;

        //         foreach ($data as $item) {
        //             foreach ($months as $month) {
        //                 $value = isset($item[$month]) ? (float)$item[$month] : 0;
        //                 $result[$month] += $value;
        //                 $grandTotal += $value;
        //             }
        //         }

        //         $result['total'] = $grandTotal;
        //         return $result;
        //     }

        //     $employees = Employee::with('timeAllocations')
        //     ->has('timeAllocations') // s'assure que l'employé a des timeAllocations
        //     ->get();

        //     // Création des monthlyTotals
        //     foreach ($employees as $employee) {
        //         $data = $employee->timeAllocations->toArray(); // récupère les données des allocations
        //         $totals = calculateMonthlyTotals($data, $months);
        //         $totals['employeeId']= $employee->employeeId;
        //         // Créer le monthly total
        //         $totals['year'] =  Carbon::now()->year;;
        //         $employee->monthlyTotal()->create($totals);
        //     }
        // }
        // monthlyTota();
    }
}
