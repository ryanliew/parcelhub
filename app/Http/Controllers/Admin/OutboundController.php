<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lot;
use App\Notifications\OutboundStatusUpdateNotification;
use App\Outbound;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutboundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Controller::VueTableListResult( Outbound::select("*") );
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
        //
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
    public function update(Request $request)
    {
        $this->validate($request, ['process_status' => 'required']);

        $outbound = outbound::find($request->id);

        if($outbound->process_status == "canceled")
        {
            return response(json_encode(array('process_status' => ['Outbound has been canceled.'])), 422);
        }

        if(($outbound->process_status !== "pending" && $outbound->process_status !== "processing") && $request->process_status == "canceled")
        {
            return response(json_encode(array('process_status' => ['Outbound has already been delivered.'])), 422);
        }

        if($request->process_status == 'canceled') {
            // Remove the lot product and its quantity
            foreach($outbound->products as $product)
            {
                $lot = Lot::find($product->pivot->lot_id);
                $lot_product = $lot->products()->where('product_id', $product->id)->first();
                $new_outgoing_quantity = $lot_product->pivot->outgoing_product - $product->pivot->quantity;
                $lot->products()->updateExistingPivot($product->id, ['outgoing_product' => $new_outgoing_quantity]);
                
                $lot->left_volume = $lot->left_volume - ($product->volume * $product->pivot->quantity);
                $lot->save();
            }
        }

        if($request->process_status == 'completed' || $request->process_status == 'delivered') {
            // Remove the lot product and its quantity
            foreach($outbound->products as $product)
            {
                if($product->total_quantity < $product->pivot->quantity)
                {
                    return response(json_encode(array('process_status' => ['We do not have enough ' . $product->name . ' in the warehouse.'])), 422);
                }                
            }
        }
        
        if($request->process_status == 'completed') {
            foreach($outbound->products as $product) {
                $lot = Lot::find($product->pivot->lot_id);
                $lot_product = $lot->products()->where('product_id', $product->id)->first();
                $new_outgoing_quantity = $lot_product->pivot->outgoing_product - $product->pivot->quantity;
                $new_quantity = $lot_product->pivot->quantity - $product->pivot->quantity;

                $lot->products()->updateExistingPivot($product->id, ['outgoing_product' => $new_outgoing_quantity, 'quantity' => $new_quantity]);
                $lot->save();
            }
        }

        $outbound->process_status = $request->process_status;
        $outbound->save();

        if(Entrust::hasRole('admin')) {

            $outbound->user->notify(new OutboundStatusUpdateNotification($outbound));

        } else {

            if($outbound->process_status == 'canceled') {

                User::admin()->first()->notify(new OutboundStatusUpdateNotification($outbound));
            }

            $outbound->user->notify(new OutboundStatusUpdateNotification($outbound));
        }

        if(request()->wantsJson())
        {   
            return ["message" => "Outbound updated successfully."];
        }

        return redirect()->back()->withSuccess($outbound->name . ' updated successfully.');
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
