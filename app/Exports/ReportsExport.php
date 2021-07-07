<?php

namespace App\Exports;

use App\Reports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportsExport implements FromView
{
    public $products, $to, $from, $type, $details;

    function __construct($products, $to, $from, $type, $details) {
        $this->products = $products;
        $this->to = $to;
        $this->from = $from;
        $this->type = $type;
        $this->details = $details;
    }

    public function view(): View{
        return view('report.excel-stock', ['products' => $this->products, 'from' => $this->from, 'to' => $this->to, 'type' => $this->type, 'details' => $this->details]);
    }
}
