<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lot;
use App\Outbound;
use Illuminate\Http\Request;

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
                if(is_null($lot_product))
                {
                    $lot->products()->attach($product, ['quantity' => $product->pivot->quantity]);
                }
                else
                {
                    $new_quantity = $lot_product->pivot->quantity + $product->pivot->quantity;
                    $lot->products()->updateExistingPivot($product, ['quantity' => $new_quantity]);
                }
                $lot->left_volume = $lot->left_volume - ($product->volume * $product->pivot->quantity);
                $lot->save();
            }
        }

        $outbound->process_status = $request->process_status;
        $outbound->save();

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
