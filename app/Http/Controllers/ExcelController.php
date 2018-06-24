<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Customer;
use App\Http\Controllers\InboundController;
use App\Inbound;
use App\Notifications\Admin\InboundCreatedNotification;
use App\Notifications\Admin\OutboundCreatedNotification;
use App\Outbound;
use App\Product;
use App\User;
use App\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ExcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('excel.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'file' => 'required'
        ]);
        
        $excelRows = Excel::load($request->file('file'))->noHeading()->skipRows(3)->toArray();
        foreach($excelRows as $excelRow){
            $product = Product::firstOrCreate(
                 ['sku' => $excelRow[0]],
                 ['sku' => $excelRow[0],
                'name' => $excelRow[1],
                'height' => $excelRow[2],
                'length' => $excelRow[3],
                'width' => $excelRow[4],
                'is_dangerous' => strtolower($excelRow[5]) == 'yes',
                'is_fragile' => strtolower($excelRow[6]) == 'yes',
                'trash_hole' => $excelRow[7],
                'user_id' => auth()->id()
            ]);
        }

        return ["message" => "Products uploaded successfully", "number" => sizeof($excelRows)];
    }

    public function processOutbound(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);

        $excelRows = Excel::load($request->file('file'))->noHeading()->skipRows(2)->toArray();
        $details = collect([]);



        // Save product and customer into processing queue
        foreach($excelRows as $excelRow) 
        {
            $detail = [];

            if(!is_null($excelRow[3])) {
                
                $customer = auth()->user()->customers()->updateOrCreate(
                ['customer_name' => $excelRow[3]],
                [
                    'customer_name' => $excelRow[3],
                    'customer_address' => $excelRow[5],
                    'customer_address_2' => $excelRow[6],
                    'customer_phone' => $excelRow[10],
                    'customer_postcode' => $excelRow[8],
                    'customer_state' => $excelRow[7],
                    'customer_country' => $excelRow[9],
                ]);

                if(strtolower($customer->customer_country) !== 'malaysia')
                {
                    return response(json_encode(array('overall' => ['Customer ' . $excelRow[3] . ' does not have a Malaysia address. We only support excel import for Malaysia outbound for now. Please manually create a foreign outbound from the "Outbounds" page.'])), 422);
                }

                $product = Product::where('sku', $excelRow[1])->first();
                if(is_null($product))
                {
                    return response(json_encode(array('overall' => ['Product ' . $excelRow[1] . ' not found. Please create your product at "My Products" page first.'])), 422);
                }

                $courier = Courier::where('name', 'LIKE', '%' . $excelRow[4] . '%')->first();
                if(is_null($courier))
                {
                    return response(json_encode(array('overall' => ['Courier ' . $excelRow[4] . ' not found. Please check with our administrator.'])), 422);
                }

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
        $count = 0;
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

            $count++;
        }

        User::admin()->first()->notify(new OutboundCreatedNotification());
        return response()->json(['message' => $count . ' outbound orders created successfully']); 

    }

    public function processInbound(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);

        $excelRows = Excel::load($request->file('file'))->noHeading()->skipRows(2)->toArray();

        $details = collect([]);
        $count = 0;
        foreach($excelRows as $excelRow) 
        {
            $detail = [];
            if(!is_null($excelRow[1]))
            {
                if(Carbon::parse($excelRow[1])->lt(Carbon::now()))
                {
                    return response(json_encode(array('overall' => ['Arrival date ' . $excelRow[1] . ' is a past date.'])), 422);
                }
                
                $product = Product::where('sku', $excelRow[2])->first();
                if(is_null($product))
                {
                    return response(json_encode(array('overall' => ['Product ' . $excelRow[2] . ' not found. Please create your product at "My Products" page first.'])), 422);
                }

                $detail['arrival'] = $excelRow[1];
                $detail['carton'] = $excelRow[3];
                $detail['product'] = $product; 
                $detail['remark'] = $excelRow[6];
                $detail['expiry'] = is_null($excelRow[5]) ? null : $excelRow[5];
                $detail['quantity'] = $excelRow[4];

                $details->push($detail);
            }
        }

        $arrivals = $details->unique('arrival');

        foreach($arrivals as $arrival)
        {
            $current = $details->filter(function ($value, $key) use ($arrival){ return $value['arrival'] == $arrival['arrival']; });

            $current_carton = $current->first()['carton'];


            $inbound = new Inbound();
            $inbound->user_id = auth()->id();
            $inbound->arrival_date = $arrival['arrival'];
            $inbound->total_carton = $current_carton;
            $inbound->status = "true";
            $inbound->save(); 

            $count++;
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

        User::admin()->first()->notify(new InboundCreatedNotification());
        return response()->json(['message' => $count . ' inbound orders created successfully']); 

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function uploadPhotos(Request $request)
    {
        $filename = explode('.', $request->file->getClientOriginalName())[0];
        $product = Product::where('SKU', $filename)->first();         

        if(!is_null($product)){
            $file = $request->file;
            $path = $file->hashName('public');

            $image = Image::make($file);
            $image->resize(300,null, function($constraint) {
                $constraint->aspectRatio();
            });
             Storage::put($path, (string) $image->encode());
             $product->picture = $path;

            //$product->picture = $request->file->store('public');
            $product->save();
        }
    }

    public function download()
    {
        $path = storage_path('app/public/parcelhub_products_import.xlsx');

        return response()->download($path);
    }

    public function downloadOutbound()
    {
        $path = storage_path('app/public/parcelhub_outbounds_import.xlsx');

        return response()->download($path);
    }

    public function downloadInbound()
    {
        $path = storage_path('app/public/parcelhub_inbounds_import.xlsx');

        return response()->download($path);
    }
}
