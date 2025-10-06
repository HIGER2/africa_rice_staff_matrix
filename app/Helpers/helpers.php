<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class helpers
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    static public function extractDataXlsx($filePath)
    {
        $data = [];
        $new = [];
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

        return $data;
    }

    static public  function normalizeNumber($value): int|float
    {
        if (is_null($value) || $value === '' || !is_numeric($value)) {
            return 0;
        }
        return $value;
    }
}
