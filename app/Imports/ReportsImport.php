<?php

namespace App\Imports;

use App\Reports;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithStartRow;

HeadingRowFormatter::default('none');

class ReportsImport implements ToModel
{
    public $request;

    function __construct($request) {
        $this->request = $request;
    }
    public function model(): array
    {
        $excelRows = $this->request->file('file');

        foreach($excelRows as $excelRow){
            if(!is_null($excelRow[0])) {
                $product = Product::updateOrCreate(
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
        }

        return new Reports([
            $product
        ]);

    }
    public function startRow(): int{
        return 3;
    }
}
