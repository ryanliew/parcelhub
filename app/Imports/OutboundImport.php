<?php

namespace App\Imports;

use App\Outbound;
use App\User;
use App\Rules\ProductHasLot;
use App\Rules\HasCourier;
use App\Product;
use App\Courier;
use App\Branch;
use App\Events\EventTrigger;
use App\Notifications\Admin\OutboundCreatedNotification as AdminOutboundCreatedNotification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

HeadingRowFormatter::default('none');

class OutboundImport implements ToCollection, WithStartRow, SkipsEmptyRows, WithValidation
{
    
    public function collection(Collection $rows)
    {
        $details = collect([]);
        $array = [];

        // Save product and customer into processing queue
        foreach($rows as $excelRow) 
        {
            $detail = [];
            
            if(!is_null($excelRow[4])) {
                $customer = auth()->user()->customers()->updateOrCreate(
                    [
                        'customer_name' => $excelRow[4],
                        'customer_address' => $excelRow[6],
                        'customer_address_2' => $excelRow[7],
                        'customer_phone' => $excelRow[11],
                        'customer_postcode' => $excelRow[9],
                        'customer_state' => $excelRow[8],
                        'customer_country' => $excelRow[10],
                    ]);
                $product = Product::where('sku', $excelRow[1])
                            ->where('status', 'true')
                            ->where('user_id', auth()->id())
                            ->first();
                $branch = Branch::select('branches.id')
                            ->leftJoin('lots' , 'lots.branch_id', '=', 'branches.id')
                            ->where('lots.user_id', auth()->id())
                            ->where('branches.codename', $excelRow[3])
                            ->first();
                $courier = Courier::where('name', 'LIKE', '%' . $excelRow[5] . '%')->first();
                $detail['customer'] = $customer;
                $detail['product'] = $product;
                $detail['quantity'] = $excelRow[2];
                $detail['branchCode'] = $branch->id;
                $detail['courier'] = $excelRow[5];
                $detail['no'] = $excelRow[0];
                $detail['remark'] = $excelRow[12];
                $detail['courier'] = $courier;
                $details->push($detail);
                
                array_push($array, $branch->id);
            }
        }
 
        $collection_user = collect($array)->unique();

        $numbers = $details->unique('no');

        foreach($numbers as $number)
        {
            $current = $details->filter(function ($value, $key) use ($number){ return $value['no'] == $number['no']; });
            
            $current_customer = $current->first()['customer'];
            $current_courier = $current->first()['courier'];
            $outbound = new Outbound();
            $outbound->insurance = false;
            $outbound->amount_insured = 0;
            
            $outbound->user_id = auth()->user()->id;
            $outbound->is_business = false;
            $outbound->status = 'true' ;
            $outbound->process_status = 'pending';

            $outbound->recipient_name = $current_customer->customer_name;
            $outbound->branch_id = $number['branchCode'];
            $outbound->recipient_address = $current_customer->customer_address;
            $outbound->recipient_address_2 = $current_customer->customer_address_2;
            $outbound->recipient_phone = $current_customer->customer_phone;
            $outbound->recipient_postcode = $current_customer->customer_postcode;
            $outbound->recipient_state = $current_customer->customer_state;
            $outbound->recipient_country = $current_customer->customer_country;

            $outbound->courier_id = $current_courier->id;
            $outbound->save();

            foreach($current->all() as $outbound_product)
            {
                $product = $outbound_product['product'];
                $quantity = $outbound_product['quantity'];

                foreach($product->lots as $lot)
                {
                    $available_quantity = $lot->pivot->quantity + $lot->pivot->incoming_quantity - $lot->pivot->outgoing_product;

                    if($available_quantity >= $quantity)
                    {
                        // We are at a situation where we can get everything from this lot
                        $new_lot_volume = $lot->left_volume + ($product->volume * $quantity);

                        $lot->update(['left_volume' => $new_lot_volume]);

                        $new_outgoing_quantity = $lot->pivot->outgoing_product + $quantity;

                        $product->lots()->updateExistingPivot($lot->id, ['outgoing_product' => $new_outgoing_quantity]);

                        $outbound->products()->attach($outbound_product['product']->id, ['quantity' => $outbound_product['quantity'], 'remark' => $outbound_product['remark'], 'lot_id' => $lot->id]);

                        break;
                    }
                    else if($available_quantity > 0)
                    {
                        // We are at a situation where we need to get something from this lot but still need to move to next lot
                        // Meaning, take everything out from this lot

                        $new_lot_volume = $lot->left_volume + ($product->volume * $lot->pivot->quantity);

                        $lot->update(['left_volume' => $new_lot_volume]);

                        $new_outgoing_quantity = $lot->pivot->outgoing_product + $available_quantity;

                        $product->lots()->updateExistingPivot($lot->id, ['outgoing_product' => $new_outgoing_quantity]);

                        $outbound->products()->attach($outbound_product['product']->id, ['quantity' => $available_quantity, 'remark' => $outbound_product['remark'], 'lot_id' => $lot->id]);

                        $quantity -= $available_quantity;
                    }
                }   
            }
        }
        $outbound->notify(new OutboundStatusUpdateNotification($outbound));
        event(new EventTrigger('outbound'));    
    }

    public function rules(): array {
        return[
            '10' => Rule::in(['malaysia', 'Malaysia', 'MALAYSIA']),
            '1' => new ProductHasLot,
            '5' => new HasCourier,
            '3' => "exists:branches,codename",
        ];
    }

    public function customValidationMessages()
    {
        return[
            '10.in' => 'Customer :input does not have a Malaysia address. We only support excel import for Malaysia outbound for now. Please manually create a foreign outbound from the "Outbounds" page.',
            '3.exists' => 'Branch :input not found. Please put a valid branch code',
        ];
    }

    public function startRow(): int{
        return 3;
    }
}
