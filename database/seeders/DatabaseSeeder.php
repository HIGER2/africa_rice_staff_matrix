<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        function calculateMonthlyTotals(array $data, array $months): array {
            $result = array_fill_keys($months, 0);
            $grandTotal = 0;

            foreach ($data as $item) {
                foreach ($months as $month) {
                    $value = isset($item[$month]) ? (float)$item[$month] : 0;
                    $result[$month] += $value;
                    $grandTotal += $value;
                }
            }

            $result['total'] = $grandTotal;
            return $result;
        }

        $employees = Employee::with('timeAllocations')
        ->has('timeAllocations') // s'assure que l'employé a des timeAllocations
        ->get();

                    // Création des monthlyTotals
            foreach ($employees as $employee) {
                $data = $employee->timeAllocations->toArray(); // récupère les données des allocations
                $totals = calculateMonthlyTotals($data, $months);
                $totals['employeeId']= $employee->employeeId;
                // Créer le monthly total
                $employee->monthlyTotal()->create($totals);
            }
    }
}
