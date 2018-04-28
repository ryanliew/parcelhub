<?php
namespace App\Http\Controllers;
use App\Inbound;
use App\InboundProduct;
use App\Lot;
use App\Notifications\InboundCreatedNotification;
use App\Product;
use App\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Settings;
class InboundController extends Controller
{
    protected $rules = [
        'arrival_date' => 'required',
        'total_carton' => 'required',
        'products'  => 'required'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

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
                                                                ->where('inbounds.type', 'inbound')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
                                                                ->orderBy('arrival_date', 'desc'));
            else
                return Controller::VueTableListResult(auth()->user()->inbounds()->with('products', 'products_with_lots.lots')->where('inbounds.type', 'inbound')->orderBy('arrival_date', 'desc'));
        }
        $inbounds = inbound::where('status', 'true')->get();
        $products = product::where('user_id', auth()->user()->id)->where('status', 'true')->get();
        return view('inbound.index')->with('inbounds', $inbounds)->with('products', $products);
    }

    public function indexToday()
    {
        return Controller::VueTableListResult(Inbound::with('products', 'products_with_lots.lots')
                                                                ->select('arrival_date',
                                                                        'total_carton',
                                                                        'process_status',
                                                                        'inbounds.id as id',
                                                                        'users.name as customer',
                                                                        'inbounds.created_at as created_at'
                                                                        )
                                                                ->where('inbounds.type', 'inbound')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
                                                                ->whereDate('arrival_date', DB::raw('CURDATE()'))
                                                                ->orderBy('arrival_date', 'desc'));
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

    public function report($id)
    {
        $inbound = Inbound::find($id);

        $pdf = PDF::loadView('inbound.report', compact('inbound'));

        $filename = Inbound::prefix() . $inbound->id . '.pdf';
        return $pdf->setPaper('A4')->download($filename);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules, ['products.required' => "Please select at least 1 product."]);
        $auth = auth()->user();

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
        $rproducts = json_decode(request()->products);
        foreach($rproducts as $product){
            $product_volume_from_db = product::where('id', $product->id)->first();
            $product_total_volume = $product_total_volume + ($product_volume_from_db->volume * $product->quantity);
            $products[$product->id] = [];
            $products[$product->id]["quantity"] = $product->quantity;
            $products[$product->id]["volume"] = $product_volume_from_db->volume * $product->quantity;
            $products[$product->id]["remark"] = $product->remark;
            $products[$product->id]["expiry_date"] = $product->expiry_date;
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
        $inbound->status = "true";
        $inbound->save();
        // Insert products into many to many table
        foreach($products as $key => $product){
            $inbound->products()->attach($key, ['quantity' => $product["quantity"], 
                                                'expiry_date' => $product["expiry_date"] ? $product["expiry_date"] : null, 
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
                        $this->attachLot($inbound->id, $key, $lot, $product["expiry_date"], $quantityIntoLot);
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
                        $inboundproduct = $this->attachLot($inbound->id, $key, $lot, $product["expiry_date"], $quantityIntoLot);
                        
                    }
                }
            }
            $lot->save();
            $lot->products()->attach($lot_products);
            $lot->propagate_left_volume();
        }

        Auth::user()->notify(new InboundCreatedNotification($inbound));

        return ['message' => "Inbound order created"];
    }

    public function calculateQuantity($volume, $singleVolume, $quantity){
        $quantityIntoLot = floor($volume / $singleVolume);
        return min($quantityIntoLot, $quantity);
    }

    public function attachLot($inbound, $product, $lot, $expiry_date, $quantity) {
        // Attach lot to inbound product
        $inbound_product = InboundProduct::where('inbound_id', $inbound)->where('product_id', $product)->first();
        $inbound_product->lots()->attach($lot, ['quantity_original' => $quantity, 'expiry_date' => $expiry_date ? $expiry_date : null]);

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
        return inbound::with(['products', 'products_with_lots.lots'])->where('id', $id)->first();

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
        $products = json_decode($request->products);
        
        // We need to validate all the lots volume first
        foreach($products as $product)
        {
            $the_product = Product::find($product->product_id);

            $unique = collect($product->lots)->pluck('lot.value')->unique();

            if($unique->count() !== collect($product->lots)->count())
            {
                return response(json_encode(array('products' => ['You have repeating lots defined in ' . $the_product->name . '.'])), 422);
            }

            foreach($product->lots as $lot)
            {
                if($lot->original_lot !== $lot->lot->value)
                {                
                    $new_lot = Lot::find($lot->lot->value);

                    $volume_required = $lot->quantity_received * $the_product->volume;

                    if($new_lot->left_volume < $volume_required)
                    {
                        return response(json_encode(array('products' => ['You only have ' . Utilities::convertCentimeterCubeToMeterCube($new_lot->left_volume) . 'm³ of space left in ' . $new_lot->name . ' but you are trying to fit in ' . Utilities::convertCentimeterCubeToMeterCube($volume_required) . 'm³.'])), 422);
                    }
                }
            }
        }

        // Validate complete

        foreach($products as $product)
        {

            $inbound_product = InboundProduct::find($product->inbound_product_id);

            $inbound_product->lots()->sync(collect($product->lots)->pluck('lot.value'));

            foreach($product->lots as $lot)
            {
                $original_lot = Lot::find($lot->original_lot);

                $original_lot->deduct_incoming_product($inbound_product->product, $lot->original_quantity);

                $original_lot->propagate_left_volume();

                $new_lot = Lot::find($lot->lot->value);

                $new_lot->increase_incoming_product($inbound_product->product, $lot->quantity_received); 

                $new_lot->propagate_left_volume();

                $inbound_product->lots()->updateExistingPivot($lot->lot->value, [
                        'quantity_received' => $lot->quantity_received,
                        'expiry_date' => $lot->expiry_date,
                        'remark' => $lot->remark
                    ]);
            }
        }

        return ['message' => 'Inbound details updated successfully'];
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