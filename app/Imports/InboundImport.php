<?php

namespace App\Imports;

use App\Inbound;
use App\Product;
use App\Courier;
use App\Branch;
use App\User;
use App\Accessibility;
use App\Rules\PresentDate;
use Carbon\Carbon;
use App\Utilities;
use App\Events\EventTrigger;
use App\Notifications\Admin\InboundCreatedNotification as AdminInboundCreatedNotification;
use App\Notifications\InboundCreatedNotification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Controllers\InboundController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

HeadingRowFormatter::default('none');

class InboundImport implements ToCollection, WithStartRow, SkipsEmptyRows, WithValidation
{
    public function collection(Collection $rows)
    {
        $details = collect([]);
        $array = [];

        foreach($rows as $excelRow) 
        {
            $detail = [];
            
            if(!is_null($excelRow[1]))
            {
                
                $product = Product::where('sku', $excelRow[3])
                                ->where('status', 'true')
                                ->where('user_id', auth()->id())
                                ->first();
                $branch = Branch::select('branches.code', 'branches.name')
                                ->leftJoin(env('DB_DATABASE').'.lots as lots' , 'lots.branch_code', '=', 'branches.code')
                                ->where('lots.user_id', auth()->id())
                                ->where('branches.code', $excelRow[2])
                                ->first();

                $detail['arrival'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelRow[1]);
                $detail['branchCode'] = $branch->code;
                $detail['carton'] = $excelRow[4];
                $detail['product'] = $product; 
                $detail['remark'] = $excelRow[7];
                $detail['expiry'] = is_null($excelRow[6]) ? null : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelRow[5]);
                $detail['quantity'] = $excelRow[5];

                $details->push($detail);
                array_push($array, $branch->id);
            }
        }
        $collection_user = collect($array)->unique();
        $arrivals = $details->unique('arrival');

        foreach($arrivals as $arrival)
        {
            $current = $details->filter(function ($value, $key) use ($arrival){ return $value['arrival'] == $arrival['arrival']; });

            // We should sum up total carton instead of getting the first one
            // $current_carton = $current->first()['carton'];
            $current_carton = $current->sum('carton');

            $inbound = new Inbound();
            $inbound->user_id = auth()->id();
            $inbound->arrival_date = $arrival['arrival'];
            $inbound->total_carton = $current_carton;
            $inbound->branch_code = $arrival['branchCode'];
            $inbound->status = "true";
            $inbound->save(); 

            foreach($current->all() as $inboundProduct)
            {
                $product = $inboundProduct['product'];
                $quantity = $inboundProduct['quantity'];

                $inbound->products()->attach($product->id, [
                                            'quantity' => $quantity, 
                                            "expiry_date" => $inboundProduct['expiry'], 
                                            "remark" => $inboundProduct['remark']
                                        ]);

                $lots = $product->lots()->get();
                $single_volume = Utilities::convertCentimeterCubeToMeterCube($product->volume);
                foreach($lots as $lot)
                {
                    if($quantity > 0) 
                    {
                        $total_volume = $quantity * $single_volume;
                        $quantityIntoLot =  InboundController::CALCULATE_QUANTITY($total_volume, $single_volume, $quantity);

                        if($quantityIntoLot > 0) 
                        {
                            $new_incoming_quantity = $lot->pivot->incoming_quantity + $quantityIntoLot;
                            $quantity -= $quantityIntoLot;
                            InboundController::ATTACH_LOT($inbound->id, $product->id, $lot, $inboundProduct['expiry'], $quantityIntoLot);
                            $lot->products()->updateExistingPivot($product->id, ["incoming_quantity" => $new_incoming_quantity]);
                        } 
                    }
                }

                foreach(auth()->user()->lots()->get() as $lot_key => $lot)
                {
                    $lot_products = [];
                    if($quantity > 0) 
                    {
                        $total_volume = $quantity * $single_volume;
                        $quantityIntoLot = InboundController::CALCULATE_QUANTITY($total_volume, $single_volume, $quantity);

                        if($lot_key + 1 == auth()->user()->lots()->count()) $quantityIntoLot = $quantity;

                        if($quantityIntoLot > 0) 
                        {
                            $new_incoming_quantity = $quantityIntoLot;
                            $existing_lot_product = $lot->products()->where('id', $product->id)->first();
                            if($existing_lot_product)
                            {
                                $new_incoming_quantity += $existing_lot_product->pivot->incoming_quantity;
                                $existing_lot_product->lots()->updateExistingPivot($lot->id, ["incoming_quantity" => $new_incoming_quantity]);
                            }
                            else
                            {
                                $lot->products()->attach($product->id, ["incoming_quantity" => $new_incoming_quantity]) ;
                            }

                            $quantity -= $quantityIntoLot;
                            InboundController::ATTACH_LOT($inbound->id, $product->id, $lot, $inboundProduct['expiry'], $quantityIntoLot);
                        } 

                        $lot->propagate_left_volume();
                    }
                }
            } 
        }
        $inbound->notify(new InboundCreatedNotification($inbound), new AdminInboundCreatedNotification($inbound));
        event(new EventTrigger('inbound')); 
    }

    public function rules(): array {
        return[
            '1' => new PresentDate,
            '2' => "exists:branches,codename",
            '3' => "exists:products,sku"
        ];
    }

    public function customValidationMessages()
    {
        return [
            '2.exists' => 'Branch :input not found. Please put a valid branches',
            '3.exists' => 'Product :input not found. Please create your product at "My Products" page first.'
        ];
    }

    public function startRow(): int{
        return 3;
    }
}
