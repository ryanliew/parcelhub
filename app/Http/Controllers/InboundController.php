<?php

namespace App\Http\Controllers;

use Settings;
use App\Lot;
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
        $user_lots = lot::where('volume','>', 0)->where('user_id', $auth->id)->get();
        $product_collections = collect();
        $collection_index = 0;
        $product_total_volume = 0;
        $products['1'] = ['1']; // $products[product_id] = [quantity]
        $products['2'] = ['20']; // $products[product_id] = [quantity]
        // $products['3'] = ['2'];

        foreach($products as $key => $product){
            $product_volume_from_db = product::where('id', $key)->first();
            $product_total_volume = $product_total_volume + ($product_volume_from_db->volume * $product[0]); 
            array_push($products[$key], $product_volume_from_db->volume * $product[0]);
            array_push($products[$key], $product_volume_from_db->volume);
        }

        
        /*  NOTE: 
        products[0] = requiredQuantity
        products[1] = requiredVolume
        products[2] = eachVolume */

        if($product_total_volume <= $user_lots->sum('leftvolume')){
            if($compare->diffInDays($now) > Settings::get('days_before_order') ){
                $inbound = new inbound;
                $inbound->user_id = $auth->id;
                $inbound->product = $request->product;
                $inbound->quantity = $request->quantity;
                $inbound->arrival_date = $request->date;
                $inbound->total_carton = $request->carton;
                $inbound->status = "true";
                $inbound->save();
                foreach($products as $key => $product){
                    $inbound->products()->attach($key, ['quantity' => $product[0]]);
                }
                foreach($user_lots as $lot){   // 1, 2
                    echo "<br>: " . $lot->id;
                    foreach($products as $key => $product){ //1, 2
                        if($lot->leftvolume > $product[1] && $product[1] > 0 && $product[0] > 0 && $lot->leftvolume > 0){
                            echo "<br> all in";
                            $lot->products()->attach($key, ['quantity' => $product[0]]);
                            $lot->leftvolume = $lot->leftvolume - $product[1];
                            $lot->save();
                            $products[$key][0] = 0;
                            $products[$key][1] = 0;
                        } else {
                            if($product[1] > 0 && $product[0] > 0){
                                $potentialQuantity = round($lot->leftvolume / $product[1] * $product[0], 0, PHP_ROUND_HALF_DOWN);
                                echo "<br> potentialQuantity: " . $potentialQuantity;
                                if($potentialQuantity > 0){
                                    echo "<br> here:";
                                    $lot->products()->attach($key, ['quantity' => $potentialQuantity]);
                                    $lot->leftvolume = $lot->leftvolume - ($potentialQuantity * $product[2]);
                                    $lot->save();
                                    $products[$key][1] = $product[1] - $product[2] * $potentialQuantity;
                                    $products[$key][0] = $product[0] - $potentialQuantity;
                                }
                            }
                        }
                    }
                }
            } else {
                return redirect()->back()->withErrors("Inbound must be created before ".Settings::get('days_before_order')." days.");
            }
        } else {
            return redirect()->back()->withErrors("You have exceeded your lot limit.");
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
        $inbound = inbound::where('id', $id)->first();
        // $arrivalDate = $inbound->arrival_date->format('Y.m.d');

        // dd($arrivalDate);
        
        return view('inbound.show')->with('inbound', $inbound);
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
