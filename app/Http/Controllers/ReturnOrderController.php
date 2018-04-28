<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inbound;
use App\InboundProduct;
use App\Lot;
use App\Notifications\InboundCreatedNotification;
use App\Product;
use App\Utilities;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Settings;

class ReturnOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->wantsJson())
        {
            $user = auth()->user();
            if($user->hasRole('admin'))
                 return Controller::VueTableListResult(Inbound::with('products', 'products_with_lots.lots')
                                                                ->select('arrival_date',
                                                                        'total_carton',
                                                                        'process_status',
                                                                        'inbounds.id as id',
                                                                        'users.name as customer',
                                                                        'inbounds.created_at as created_at'
                                                                        )
                                                                ->where('type', 'return')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
                                                                ->orderBy('arrival_date', 'desc'));
            else
                return Controller::VueTableListResult(auth()->user()->inbounds()->with('products', 'products_with_lots.lots')->orderBy('arrival_date', 'desc'));
        }
        $inbounds = inbound::where('status', 'true')->get();
        $products = product::where('user_id', auth()->user()->id)->where('status', 'true')->get();
        return view('inbound.index')->with('inbounds', $inbounds)->with('products', $products);
    }

    public function page()
    {
        return view('return.page');
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
        if($request->user_id){
            $auth = User::find($request->user_id);
        }
        
        if(empty(auth()->user()->address))
        {
            return response(json_encode(array('overall' => ['You must update your contact details in the My Profile page before proceeding'])), 422);
        }

        $now = Carbon::today();
        $compare = Carbon::parse($request->arrival_date);
        $user_lots = $auth->lots()->where('volume','>', 0)->where('status', 'approved')->get();
        $collection_index = 0;
        $product_total_volume = 0;
        $products = [];
        $rproducts = json_decode(request()->return_products);
        foreach($rproducts as $product){
            $product_volume_from_db = product::where('id', $product->id)->first();
            $product_total_volume = $product_total_volume + ($product_volume_from_db->volume * $product->quantity);
            $products[$product->id] = [];
            $products[$product->id]["quantity"] = $product->quantity;
            $products[$product->id]["volume"] = $product_volume_from_db->volume * $product->quantity;
            $products[$product->id]["remark"] = $product->remarks;
            $products[$product->id]["singleVolume"] = $product_volume_from_db->volume;
        }
        // Check for quantity
        if($product_total_volume == 0){
            if(request()->wantsJson()) {
                return response(json_encode(array('products' => ['Please select at least 1 product'])), 422);
            }
            return redirect()->back()->withErrors("You need to have at least 1 product");
        }

        $left_volume = $user_lots->sum('left_volume');
        // Check for total left over volume
        if($product_total_volume > $left_volume){
            if(request()->wantsJson()) {
                return response(json_encode(array('products' => ['You only have ' . Utilities::convertCentimeterCubeToMeterCube($left_volume) . 'm³ of space left but you are trying to fit in ' . Utilities::convertCentimeterCubeToMeterCube($product_total_volume) . 'm³. Please purchase more lots.'])), 422);
            }
            return redirect()->back()->withErrors("You have exceeded your lot limit.");
        }
        // Check for days before order
        if($compare->diffInDays($now) < Settings::get('days_before_order') ){
            if(request()->wantsJson()) {
                return response(json_encode(array('arrival_date' => ['Inbound must be created '.Settings::get('days_before_order').' day(s) before.'])), 422);
            }
            return redirect()->back()->withErrors("Inbound must be created before ".Settings::get('days_before_order')." days.");
        }
        // Everything is ok, create a new inbound
        $inbound = new Inbound();
        $inbound->user_id = $auth->id;
        $inbound->arrival_date = $request->arrival_date;
        $inbound->total_carton = $request->total_carton;
        $inbound->customer_id = $request->customer_id;
        $inbound->status = "true";
        $inbound->type = "return";
        $inbound->save();
        // Insert products into many to many table
        foreach($products as $key => $product){
            $inbound->products()->attach($key, ['quantity' => $product["quantity"], 
                                                'remark' => $product["remark"]
                                                ]);
        }
        // Prioritize every lot that already has the same product
        foreach($products as $key => $product)
        {
            $theproduct = product::find($key);
            $lots = $theproduct->lots()->where('left_volume', '>=', $product["singleVolume"])->get();
            foreach($lots as $lot)
            {
                if($product["quantity"] > 0) {
                    $quantityIntoLot = $this->calculateQuantity($lot->left_volume, $product["singleVolume"], $product["quantity"]);

                    if($quantityIntoLot > 0){
                        $lot_products[$key]['incoming_quantity'] = $quantityIntoLot;
                        $products[$key]["volume"] = $product["volume"] - ( $product["singleVolume"] * $quantityIntoLot );
                        $products[$key]["quantity"] = $product["quantity"] - $quantityIntoLot;
                        $product["quantity"] = $products[$key]["quantity"];
                        $this->attachLot($inbound->id, $key, $lot, $quantityIntoLot);
                    }
                }
                $lot->save();
                $new_quantity = $lot->pivot->incoming_quantity + $quantityIntoLot;
                $lot->products()->updateExistingPivot($key, ["incoming_quantity" => $new_quantity]);

            $lot->propagate_left_volume();
            }
        }
        // Assign products to user lots
        $user_lots_with_enough_volume = $auth->lots()->where('left_volume', '>=', $product["singleVolume"])->get();
        foreach($user_lots_with_enough_volume as $lot){
            $lot_products = [];
            foreach($products as $key => $product){
                if($product["quantity"] > 0) {
                    // If there are still volume needed to be assigned
                    $quantityIntoLot = $this->calculateQuantity($lot->left_volume, $product["singleVolume"], $product["quantity"]);
                    //dd($quantityIntoLot);
                    if($quantityIntoLot > 0){
                        $lot_products[$key]['incoming_quantity'] = $quantityIntoLot;
                        $products[$key]["volume"] = $product["volume"] - ($product["singleVolume"] * $quantityIntoLot);
                        $products[$key]["quantity"] = $product["quantity"] - $quantityIntoLot;
                        $inboundproduct = $this->attachLot($inbound->id, $key, $lot, $quantityIntoLot);
                        
                    }
                }
            }
            $lot->save();
            $lot->products()->attach($lot_products);
            $lot->propagate_left_volume();
        }

        return ['message' => "Return order created"];
    }

    public function calculateQuantity($volume, $singleVolume, $quantity){
        $quantityIntoLot = floor($volume / $singleVolume);
        return min($quantityIntoLot, $quantity);
    }

    public function attachLot($inbound, $product, $lot, $quantity) {
        // Attach lot to inbound product
        $inbound_product = InboundProduct::where('inbound_id', $inbound)->where('product_id', $product)->first();
        $inbound_product->lots()->attach($lot, ['quantity_original' => $quantity]);

        return $inbound_product;
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
