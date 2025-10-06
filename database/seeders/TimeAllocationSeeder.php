<?php

namespace Database\Seeders;

use App\Helpers\helpers;
use App\Models\Employee;
use App\Models\TimeAllocation;
use App\Services\AllocationService;
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

    function __construct(protected AllocationService $allocationService) {}

    public function run(): void
    {
        $filePath = public_path('new.xlsx');
        $this->allocationService->SaveAllocation($filePath);
    }
}
