<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Courier;
use App\Notifications\OutboundCreatedNotification;
use App\Outbound;
use App\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PDF;
use Storage;

class RecallOrderController extends Controller
{
    public function page()
    {
        return view('recall.page');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'recipient_name' => 'required',
            'recipient_address' => 'required',
            'recipient_phone' => 'required',
            'recipient_postcode' => 'required',
            'recipient_state' => 'required',
            'recipient_country' => 'required',
            'outbound_products' => 'required'
        ]);

        if(empty(auth()->user()->address))
        {
            return response(json_encode(array('overall' => ['You must update your contact details in the My Profile page before proceeding'])), 422);
        }

        try {
            $outboundProducts = json_decode($request['outbound_products'], true);

            $json_validator = Validator::make(
                ['outbound_products' => $outboundProducts],
                ['outbound_products.*' => 'bail|product_stock']
            );

            if($json_validator->fails()) {
                return response()->json($json_validator->messages(), 422);
            }

            $user = \Auth::user();

            $user->customers()->updateOrCreate(
                ['id' => $request->customer_id],
                [
                    'customer_name' => $request->recipient_name,
                    'customer_address' => $request->recipient_address,
                    'customer_address_2' => $request->recipient_address_2,
                    'customer_phone' => $request->recipient_phone,
                    'customer_postcode' => $request->recipient_postcode,
                    'customer_state' => $request->recipient_state,
                    'customer_country' => $request->recipient_country,
                ]);

            $outbound = new Outbound($request->except(['business']));
            $outbound->insurance = request()->has('insurance');
            $outbound->invoice_slip = $request->hasFile('invoice_slip') ? $request->file('invoice_slip')->store('public') : null;
            $outbound->amount_insured = $outbound->insurance ? request()->amount_insured : 0;
            $outbound->user_id = $user->id;
            $outbound->is_business = $request->business == "true" ? true : false;
            $outbound->status = 'true';
            $outbound->process_status = 'pending';
            $outbound->save();

            foreach ($outboundProducts as $outboundProduct) {
                $product = $user->products()
                    ->where('id', $outboundProduct['id'])
                    ->firstOrFail();
                // Quantity used to mark down how many number of product
                // going to retrieve from user's lots
                $quantity = $outboundProduct['quantity']; //20

                foreach ($product->lots as $lot) {

                    // Check if the lot have enough products supply to the outbound request
                    $sumOfQuantityAndIncomingQuantity = $lot->pivot->quantity + $lot->pivot->incoming_quantity - $lot->pivot->outgoing_product; //10
                    if($sumOfQuantityAndIncomingQuantity >= $quantity) {
                        // We do not need to go to next lot anymore
                        $volumeAfterDeductProduct = $lot->left_volume + ($product->volume * $quantity);

                        $lot->update(['left_volume' => $volumeAfterDeductProduct]);

                        $newQuantityForOutgoingProduct = $lot->pivot->outgoing_product + $quantity;

                        $product->lots()->updateExistingPivot($lot->id, ['outgoing_product' => $newQuantityForOutgoingProduct]);

                        $outbound->products()->attach($product->id, ['quantity' => $quantity, 'lot_id' => $lot->id, 'remark' => $outboundProduct['remarks'], 'unit_value' => $outboundProduct['unit_value'], 'total_value' => $outboundProduct['total_value'], 'weight' => $outboundProduct['weight'], 'manufacture_country' => $outboundProduct['manufacture_country']]);

                        break;

                    } else {
                        // If we still need to go to next lot to get product, means take everything out
                        $volumeAfterDeductProduct = $lot->left_volume + ($product->volume * $lot->pivot->quantity);

                        $lot->update(['left_volume' => $volumeAfterDeductProduct]);

                        $newQuantityForOutgoingProduct = $lot->pivot->outgoing_product + $sumOfQuantityAndIncomingQuantity;

                        $product->lots()->updateExistingPivot($lot->id, ['outgoing_product' => $newQuantityForOutgoingProduct]);

                        $outbound->products()->attach($product->id, ['quantity' => $lot->pivot->quantity, 'lot_id' => $lot->id, 'remark' => $outboundProduct['remarks'], 'unit_value' => $outboundProduct['unit_value'], 'total_value' => $outboundProduct['total_value'], 'weight' => $outboundProduct['weight'], 'manufacture_country' => $outboundProduct['manufacture_country']]);

                        // Update how many quantity left require to acquire from the other lot
                        $quantity -= $sumOfQuantityAndIncomingQuantity;
                    }
                }
            }

            $user->notify(new OutboundCreatedNotification($outbound));

        } catch (\Exception $exception) {

            return response()->json($exception->getMessage(), 422);

        }

        return response()->json(['message' => 'Outbound order created successfully']);
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
}
