<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ReportsImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    public function model(array $excelRow)
    {
        if(!$excelRow[0]) {
            return null;
        }
        return Product::updateOrCreate(
            ['sku' => $excelRow[0], 'user_id' => auth()->id(), 'status' => 'true'],
            ['sku' => $excelRow[0],
           'name' => $excelRow[1],
           'height' => $excelRow[2],
           'length' => $excelRow[3],
           'width' => $excelRow[4],
           'is_dangerous' => strtolower($excelRow[5]) == 'yes',
           'is_fragile' => strtolower($excelRow[6]) == 'yes',
           'trash_hole' => $excelRow[7],
           'user_id' => auth()->id(),
           'status' => 'true'
       ]); 

    }
    public function startRow(): int{
        return 3;
    }
}
