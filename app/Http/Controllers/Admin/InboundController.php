<?php

namespace App\Http\Controllers\Admin;

use App\Inbound;
use App\Notifications\InboundStatusUpdateNotification;
use App\User;
use App\UserToken;
use Entrust;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InboundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Controller::VueTableListResult(inbound::with('products', 'products_with_lots.lots'));
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

        $inbound = inbound::find($request->id);
        if($inbound->process_status == "canceled")
        {
            return response(json_encode(array('process_status' => ['Inbound has been canceled.'])), 422);
        }
       
        // Usual cancel request
        if($inbound->process_status !== 'completed' && $request->process_status == 'canceled') {
            // Remove the lot product and its quantity
            foreach($inbound->products_with_lots as $inboundproduct)
            {
                foreach($inboundproduct->lots as $lot)
                {
                    $lot->deduct_incoming_product($inboundproduct->product, $lot->pivot->quantity_original);
                    $lot->propagate_left_volume();
                }
            }
        }

        // Cancel request for completed inbound
        if($inbound->process_status == 'completed' && $request->process_status == 'canceled') {
            // Remove the lot product and its quantity
            $can_remove = true;

            foreach($inbound->products_with_lots as $inboundproduct)
            {
                foreach($inboundproduct->lots as $lot)
                {
                    if($can_remove)
                        $can_remove = $lot->check_can_deduct_product($inboundproduct->product, $lot->pivot->quantity_received);
                }
            }

            if(!$can_remove)
                return response(json_encode(array('process_status' => ['Stock has been used in confirmed outbounds, please cancel dependent outbounds first.'])), 422);

            foreach($inbound->products_with_lots as $inboundproduct)
            {
                foreach($inboundproduct->lots as $lot)
                {
                    $lot->deduct_product($inboundproduct->product, $lot->pivot->quantity_received);
                    $lot->propagate_left_volume();
                }
            }
        }

        if($request->process_status == 'completed') {
            // Remove the lot product and its quantity
            foreach($inbound->products_with_lots as $inboundproduct)
            {
                foreach($inboundproduct->lots as $lot)
                {
                    $quantity = $inbound->type == 'return' ?
                                $lot->pivot->quantity_original :
                                $lot->pivot->quantity_received;

                    $lot_product = $lot->products()->where('product_id', $inboundproduct->product_id)->first();
                    if(!is_null($lot_product))
                    {
                        $new_incoming_quantity = $lot_product->pivot->incoming_quantity - $quantity;
                        $new_quantity = $lot_product->pivot->quantity + $quantity;
                        $lot->products()->updateExistingPivot($lot_product->id, ["incoming_quantity" => $new_incoming_quantity, "quantity" => $new_quantity]);
                    }
                    $lot->propagate_left_volume();
                }
            }
        }

        $inbound->process_status = $request->process_status;
        $inbound->save();

    if(auth()->user()->hasRole('admin')) {
            $inbound->user->notify(new InboundStatusUpdateNotification($inbound));

        } else {

            if($inbound->process_status == 'canceled') {
                $admins = User::admin()->get();
                $list_of_admin = [];
                foreach($admins as $admin) {
                    $access = $admin->branches()->where('user_id', $admin->id)->where('branch_id', $request->selectedBranch)->get();
                    if($access->count() > 0) {
                        array_push($list_of_admin, $admin);
                    }
                }
                
                if($list_of_admin != []) {
                    foreach($list_of_admin as $admin) {
                        $admin->notify(new InboundStatusUpdateNotification($inbound));
                    }
                }

                User::superadmin()->first()->notify(new InboundStatusUpdateNotification($inbound));
            }

            $inbound->user->notify(new InboundStatusUpdateNotification($inbound));
        }

        if(request()->wantsJson())
        {   
            return ["message" => "Inbound updated successfully."];
        }

        return redirect()->back()->withSuccess($inbound->name . ' updated successfully.');
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
