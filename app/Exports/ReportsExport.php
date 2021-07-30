<?php

namespace App\Exports;

use App\Reports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportsExport implements FromView
{
    public $products, $branch, $to, $from, $type, $details;

    function __construct($products, $branch, $to, $from, $type, $details) {
        $this->products = $products;
        $this->branch = $branch;
        $this->to = $to;
        $this->from = $from;
        $this->type = $type;
        $this->details = $details;
    }

    public function view(): View{
        return view('report.excel-stock', ['products' => $this->products, 'branch' => $this->branch, 'from' => $this->from, 'to' => $this->to, 'type' => $this->type, 'details' => $this->details]);
    }
}
