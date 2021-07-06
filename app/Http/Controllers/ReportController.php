<?php

namespace App\Http\Controllers;

use App\Inbound;
use App\Outbound;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportController extends Controller
{
    public function page()
    {
    	return view('report.page');
    }

    public function stockPage()
    {
        return view('report.page-stock');
    }

    public function stock()
    {
    	$this->validate(request(), [
    		'from' => 'required',
    		'to' => 'required',
    		'type' => 'required'
    	]);

    	$to = Carbon::parse(request()->to)->addDay();
    	$from = Carbon::parse(request()->from);

    	$product_id = json_decode(request()->products);

    	$products = Product::with('inbounds_with_lots.lots', 'outbounds', 'adjustments')->whereIn('id', $product_id)->get();

    	$products_with_details = collect();

    	foreach($products as $product) {

    		$details = collect();

    		// We need to pull out all the records and insert them into array for processing
    		foreach($product->inbounds_with_lots as $inbound){
                if($inbound->inbound->process_status != 'canceled') {
        			$details->push(
        				$this->formatStockDetails(
    						$inbound->updated_at, 
    						$inbound->lots->sum('pivot.quantity_received'), 
    						0, 
    						"Inbound - " . $inbound->inbound->display_no, 
    						0
    					)
        			);
                }
    		}

    		foreach($product->outbounds as $outbound) {
                if($outbound->process_status !=='canceled') {
        			$details->push(
        				$this->formatStockDetails(
        					$outbound->updated_at, 
        					0, 
        					$outbound->pivot->quantity, 
        					"Outbound - " . $outbound->display_no, 
        					0
        				)
        			);
                }
    		}

    		foreach($product->adjustments as $adjustment) {
    			$details->push(
    				$this->formatStockDetails(
    					$adjustment->created_at, 
    					0, 
    					0, 
    					"Adjustment - " . $adjustment->remark, 
    					$adjustment->new_quantity,
                        true // Is adjustment
    				)
    			);
    		}

    		$sorted = $details->sortBy('date');

    		// Update balances
    		$balance = 0;
    		foreach($sorted as $key => $detail) {
    			
    			$array = $this->formatStockDetails(
    				$detail['date'],
    				$detail['in'],
    				$detail['out'],
    				$detail['description'],
    				$balance = $detail['is_adjustment'] ? $detail['balance'] : $balance + $detail['in']  - $detail['out']
    			);

    			$sorted->put($key, $array);
    		}

    		$products_with_details->put($product->id, $sorted);

    		$product->opening = $sorted
		                        ->filter(function($record) use ($from){
		                            return $record['date']->lessThan($from);
		                        }) 
		                        ->last()['balance'];

			$product->closing = $sorted
		                        ->filter(function($record) use ($to){
		                            return $record['date']->lessThan($to);
		                        }) 
		                        ->last()['balance'];

			$product->details = $sorted
								->filter(function($record) use ($from, $to){
									return $record['date']->between($from, $to, true);
								});
    	}

        $filename = "Stock report_" . auth()->id() . "_" . $from->toDateString() . ' - ' . $to->toDateString();

        if(request()->report_type == 'excel') {

            // We need to format the excel filein array
            
            $information = Excel::create($filename, function($excel) use ($products) {
                $excel->sheet('Sheet 1', function($sheet) use ($products) {
                    $sheet->loadView('report.excel-stock', ['products' => $products, 'from' => request()->from, 'to' => request()->to, 'type' => request()->type, 'details' => request()->has('details')]);
                });
            })->store('xls', storage_path('app/public/reports/stock'), true);

            return json_encode(['message' => "Success", 'url' => 'storage/reports/stock/' . $information['file']]);

        }

    	// return view('report.stock', ['products' => $products, 'from' => request()->from, 'to' => request()->to, 'type' => request()->type, 'details' => request()->has('details')]);
    	$filename = "storage/reports/stock/" . $filename . ".pdf";
    	$pdf = PDF::loadView('report.stock', ['products' => $products, 'from' => request()->from, 'to' => request()->to, 'type' => request()->type, 'details' => request()->has('details')])->save($filename);

    	return json_encode(['message' => "Success", 'url' => $filename]);
    	// return $pdf->download("Stock report_" . $from->toDateString() . ' - ' . $to->toDateString() . ".pdf");


    }

    public function formatStockDetails($date, $in, $out, $description, $balance, $is_adjustment = false)
    {
    	return [
			"date" => $date,
			"balance" => $balance,
			"in" => $in,
			"out" => $out,
			"description" => $description,
            "is_adjustment" => $is_adjustment
		];
    }
}
