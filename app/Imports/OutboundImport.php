<?php

namespace App\Imports;

use App\Outbound;
use App\Rules\ProductHasLot;
use App\Rules\HasCourier;
use App\Product;
use App\Courier;
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

        // Save product and customer into processing queue
        foreach($rows as $excelRow) 
        {
            $detail = [];
            
            if(!is_null($excelRow[3])) {
                $customer = auth()->user()->customers()->updateOrCreate(
                    [
                        'customer_name' => $excelRow[3],
                        'customer_address' => $excelRow[5],
                        'customer_address_2' => $excelRow[6],
                        'customer_phone' => $excelRow[10],
                        'customer_postcode' => $excelRow[8],
                        'customer_state' => $excelRow[7],
                        'customer_country' => $excelRow[9],
                    ]);
                $product = Product::where('sku', $excelRow[1])
                            ->where('status', 'true')
                            ->where('user_id', auth()->id())
                            ->first();
                $courier = Courier::where('name', 'LIKE', '%' . $excelRow[4] . '%')->first();
                $detail['customer'] = $customer;
                $detail['product'] = $product;
                $detail['quantity'] = $excelRow[2];
                $detail['courier'] = $excelRow[4];
                $detail['no'] = $excelRow[0];
                $detail['remark'] = $excelRow[11];
                $detail['courier'] = $courier;
                $details->push($detail);
            }
        }

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
            
    }

    public function rules(): array {
        return[
            '9' => Rule::in(['malaysia', 'Malaysia', 'MALAYSIA']),
            '1' => new ProductHasLot,
            '4' => new HasCourier
        ];
    }

    public function customValidationMessages()
    {
        return[
            '9.in' => 'Customer :input does not have a Malaysia address. We only support excel import for Malaysia outbound for now. Please manually create a foreign outbound from the "Outbounds" page.',
        ];
    }

    public function startRow(): int{
        return 3;
    }
}
