<?php

namespace App\Http\Controllers;

use Settings;
use App\Inbound;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InboundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inbounds = inbound::where('status', 'true')->get();
        $products = product::where('user_id', auth()->user()->id)->where('status', 'true')->get();

        return view('inbound.index')->with('inbounds', $inbounds)->with('products', $products);
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
        $auth = auth()->user();
        $now = Carbon::today();
        $compare = Carbon::parse($request->date);
        $user_lot = $auth->lots;
        dd($user_lot);



        if($compare->diffInDays($now) > Settings::get('days_before_order') ){
                $inbound = new inbound;
                $inbound->user_id = $auth->id;
                $inbound->product = $request->product;
                $inbound->quantity = $request->quantity;
                $inbound->arrival_date = $request->date;
                $inbound->total_carton = $request->carton;
                $inbound->status = "true";
                $inbound->save();
                return redirect()->back()->withSuccess($request->name . " created successfully.");
            }else{
                return redirect()->back()->withErrors("Inbound must be created before ".Settings::get('days_before_order')." days.");
            }
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
