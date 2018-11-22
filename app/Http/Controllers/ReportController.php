<?php

namespace App\Http\Controllers;

use App\Inbound;
use App\Outbound;
use App\Product;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    	$products = Product::with('inbounds_with_lots.lots', 'outbounds')->whereIn('id', $product_id)->get();

    	$products_with_details = collect();

    	foreach($products as $product) {

    		$details = collect();

    		// We need to pull out all the records and insert them into array for processing
    		foreach($product->inbounds_with_lots as $inbound){
    			$details->push(
    				$this->formatStockDetails(
						$inbound->created_at, 
						$inbound->lots->sum('pivot.quantity_received'), 
						0, 
						"Inbound - " . Inbound::PREFIX() . $inbound->inbound->id, 
						0
					)
    			);
    		}

    		foreach($product->outbounds as $outbound) {
    			$details->push(
    				$this->formatStockDetails(
    					$outbound->created_at, 
    					0, 
    					$outbound->pivot->quantity, 
    					"Outbound - " . Outbound::PREFIX() . $outbound->id, 
    					0
    				)
    			);
    		}

    		foreach($product->adjustments as $adjustment) {
    			$details->push(
    				$this->formatStockDetails(
    					$adjustment->created_at, 
    					0, 
    					0, 
    					$adjustment->remark, 
    					"Adjustment - " . $adjustment->new_quantity
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
    				$balance = $detail['balance'] > 0 ?: $balance + $detail['in']  - $detail['out']
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

    	// return view('report.stock', ['products' => $products, 'from' => request()->from, 'to' => request()->to, 'type' => request()->type, 'details' => request()->has('details')]);
    	$filename = "storage/reports/stock/Stock report_" . auth()->id() . "_" . $from->toDateString() . ' - ' . $to->toDateString() . ".pdf";
    	$pdf = PDF::loadView('report.stock', ['products' => $products, 'from' => request()->from, 'to' => request()->to, 'type' => request()->type, 'details' => request()->has('details')])->save($filename);

    	return json_encode(['message' => "Success", 'url' => $filename]);
    	// return $pdf->download("Stock report_" . $from->toDateString() . ' - ' . $to->toDateString() . ".pdf");


    }

    public function formatStockDetails($date, $in, $out, $description, $balance)
    {
    	return [
			"date" => $date,
			"balance" => $balance,
			"in" => $in,
			"out" => $out,
			"description" => $description
		];
    }
}
