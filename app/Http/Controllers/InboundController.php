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
        $collection = collect();
        $collection_index = 0;
        $product_total_volume = 0;
        $products['1'] = ['1']; // $products[product_id] = [quantity]
        $products['2'] = ['20']; // $products[product_id] = [quantity]
        $products['3'] = ['2'];

        foreach($products as $val => $product){
            $product_volume_from_db = product::where('id', $val)->first();
            $product_total_volume = $product_total_volume + (($product_volume_from_db->height * $product_volume_from_db->width * $product_volume_from_db->length) * $product[0]); 
            $collection->push($val);
        }

        if($product_total_volume <= $user_lot->sum('volume')){
            if($compare->diffInDays($now) > Settings::get('days_before_order') ){
                $inbound = new inbound;
                $inbound->user_id = $auth->id;
                $inbound->product = $request->product;
                $inbound->quantity = $request->quantity;
                $inbound->arrival_date = $request->date;
                $inbound->total_carton = $request->carton;
                $inbound->status = "true";
                $inbound->save();
                $this->loopCollection($collection_index, $collection, $products, $user_lot, $inbound); 
                return redirect()->back()->withSuccess($request->name . " created successfully.");
            }else{
                return redirect()->back()->withErrors("Inbound must be created before ".Settings::get('days_before_order')." days.");
            }
        } else {
            return redirect()->back()->withErrors("You have exceeded your lot limit.");
        }
    }

    public function loopCollection($collection_index, $collection, $products, $user_lot, $inbound){
        if($collection_index < $collection->count()){
            $product_index = 0;
            $this->getProductQuantity($collection_index, $collection, $products, $collection->get($collection_index), $user_lot, $product_index, $inbound);
        }
    }   

    public function getProductQuantity($collection_index, $collection, $products, $product_id, $user_lot, $product_index, $inbound){
            $product = product::where('id', $product_id)->first();
            $product_quantity = $products[$product_id][0];
            $product_volume = $product->width * $product->height * $product->length;
            $lot_index = 0;
            if($product_index < $product_quantity){
                $this->assignProductToLot($collection_index, $collection, $products, $product_id, $user_lot, $product_index, $product_volume, $lot_index, $inbound);
            }else{
                $collection_index ++;
                $this->loopCollection($collection_index, $collection, $products, $user_lot, $inbound);
            }
        }

    public function assignProductToLot($collection_index, $collection, $products, $product_id, $user_lot, $product_index, $product_volume, $lot_index, $inbound){
        if($lot_index < $user_lot->count()){
            $lot = $user_lot->get($lot_index);
            if($product_volume <= $lot->volume){
                $inbound->products()->attach($product_id);
                $lot->products()->attach($product_id);
                $lot->volume = $lot->volume - $product_volume;
                $lot->save();
                $product_index++;
                $this->getProductQuantity($collection_index, $collection, $products, $product_id, $user_lot, $product_index, $inbound);
            }else{
                $lot_index++;
                $this->assignProductToLot($collection_index, $collection, $products, $product_id, $user_lot, $product_index, $product_volume, $lot_index, $inbound);
            }
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
