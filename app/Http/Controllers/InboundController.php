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
    protected $rules = [
        'arrival_date' => 'required',
        'total_carton' => 'required'
    ];

    /**
     * Return the view which contains the vue page for product
     * @return \Illuminate\Http\Response
     */
    public function page()
    {
        return view('inbound.page');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->wantsJson())
        {
            return Controller::VueTableListResult(inbound::with('products', 'products_with_lots.lots'));
        }
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
        $this->validate($request, $this->rules);

        $auth = auth()->user();
        $now = Carbon::today();
        $compare = Carbon::parse($request->arrival_date);
        $user_lots = lot::where('volume','>', 0)->where('user_id', $auth->id)->get();
        $collection_index = 0;
        $product_total_volume = 0;
        $products = [];
        $rproducts = json_decode(request()->products);

        foreach($rproducts as $product){
            $product_volume_from_db = product::where('id', $product->id)->first();
            $product_total_volume = $product_total_volume + ($product_volume_from_db->volume * $product->quantity);
            $products[$product->id] = [];
            array_push($products[$product->id], $product->quantity);
            array_push($products[$product->id], $product_volume_from_db->volume * $product->quantity);
            array_push($products[$product->id], $product_volume_from_db->volume);
        }
        
        /*  NOTE: 
        products[0] = requiredQuantity
        products[1] = requiredVolume
        products[2] = eachVolume */

        if($product_total_volume <= $user_lots->sum('left_volume')){
            if($compare->diffInDays($now) > Settings::get('days_before_order') ){
                $inbound = new inbound;
                $inbound->user_id = $auth->id;
                $inbound->arrival_date = $request->arrival_date;
                $inbound->total_carton = $request->total_carton;
                $inbound->status = "true";
                $inbound->save();
                foreach($products as $key => $product){
                    $inbound->products()->attach($key, ['quantity' => $product[0]]);
                }
                foreach($user_lots as $lot){ 
                    foreach($products as $key => $product){
                        if($lot->left_volume > $product[1] && $product[1] > 0 && $product[0] > 0 && $lot->left_volume > 0){                            
                            $lot->products()->attach($key, ['quantity' => $product[0]]);
                            $lot->left_volume = $lot->left_volume - $product[1];
                            $lot->save();
                            $products[$key][0] = 0;
                            $products[$key][1] = 0;
                        } else {
                            if($product[1] > 0 && $product[0] > 0){
                                $potentialQuantity = round($lot->left_volume / $product[1] * $product[0], 0, PHP_ROUND_HALF_DOWN);                                
                                if($potentialQuantity > 0){                                    
                                    $lot->products()->attach($key, ['quantity' => $potentialQuantity]);
                                    $lot->left_volume = $lot->left_volume - ($potentialQuantity * $product[2]);
                                    $lot->save();
                                    $products[$key][1] = $product[1] - $product[2] * $potentialQuantity;
                                    $products[$key][0] = $product[0] - $potentialQuantity;
                                }
                            }
                        }
                    }
                }
            } else {
                if(request()->wantsJson()) {
                    return response(['message' => "Inbound must be created before ".Settings::get('days_before_order')." days."], 422);
                }

                return redirect()->back()->withErrors("Inbound must be created before ".Settings::get('days_before_order')." days.");
            }

            return ['message' => "Inbound order created"];
        } else {
            if(request()->wantsJson()) {
                return response(['message' => "You have exceeded your lot limit"], 422);
            }
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
