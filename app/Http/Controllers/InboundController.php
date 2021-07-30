<?php
namespace App\Http\Controllers;
use App\Inbound;
use App\InboundProduct;
use App\Lot;
use App\Branch;
use App\Notifications\Admin\InboundCreatedNotification as AdminInboundCreatedNotification;
use App\Notifications\InboundCreatedNotification;
use App\Product;
use App\User;
use App\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Settings;
use App\Events\EventTrigger;
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
     * Return the view which contains the vue page for product
     * @return \Illuminate\Http\Response
     */
    public function page_excel()
    {
        return view('inbound.excel');
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

            $query = $user->inbounds()->with('products', 'products_with_lots.lots');

            if($user->hasRole('superadmin') || $user->hasRole('admin')) {
                $query = Inbound::with('products', 'products_with_lots.lots');
            }
            elseif($user->hasRole('subuser')) {
                $query = $user->parent->inbounds()->with('products', 'products_with_lots.lots');
            }
            elseif($user->hasRole('admin')) {
                return Controller::VueTableListResult($query->select('arrival_date',
                                                                'total_carton',
                                                                'type',
                                                                'process_status',
                                                                'inbounds.id as id',
                                                                'users.name as customer',
                                                                'inbounds.created_at as created_at'
                                                                )
                                                            ->where('inbounds.type', 'inbound')
                                                            ->leftJoin('users', 'user_id', '=', 'users.id')
                                                            ->join('branches', 'branches.id', '=', 'branch_id')
                                                            ->join('accessibilities', 'accessibilities.branch_id', '=', 'branches.id')
                                                            ->where('accessibilities.user_id', $user->id)
                                                            ->orderBy('arrival_date', 'desc'));
            }
            else {
                $branches = Branch::select('branches.id')
                        ->leftJoin('lots' , 'lots.branch_id', '=', 'branches.id')
                        ->where('lots.user_id', $user->id)
                        ->get();
                $array_branch = [];
                foreach($branches as $branch) {
                    array_push($array_branch, $branch->id);
                }

                return Controller::VueTableListResult($query->select('arrival_date',
                                                                'total_carton',
                                                                'type',
                                                                'process_status',
                                                                'inbounds.id as id',
                                                                'users.name as customer',
                                                                'inbounds.created_at as created_at'
                                                                )
                                                            ->where('inbounds.type', 'inbound')
                                                            ->whereIn('inbounds.branch_id', $array_branch)
                                                            ->leftJoin('users', 'user_id', '=', 'users.id')
                                                            ->orderBy('arrival_date', 'desc'));
            }
            return Controller::VueTableListResult($query->select('arrival_date',
                                                                'total_carton',
                                                                'type',
                                                                'process_status',
                                                                'inbounds.id as id',
                                                                'users.name as customer',
                                                                'inbounds.created_at as created_at'
                                                                )
                                                            ->where('inbounds.type', 'inbound')
                                                            ->leftJoin('users', 'user_id', '=', 'users.id')
                                                            ->orderBy('arrival_date', 'desc'));
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
                                                                        'type',
                                                                        'process_status',
                                                                        'inbounds.id as id',
                                                                        'users.name as customer',
                                                                        'inbounds.created_at as created_at'
                                                                        )
                                                                ->where('inbounds.type', 'inbound')
                                                                ->where('process_status', 'awaiting_arrival')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
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

        // return view('inbound.report', compact('inbound'));
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

        // Check that user will need to have at least 1 lot
        
        if($auth->lots()->count() == 0)
        {
            if(request()->wantsJson()) {
                return response(json_encode(array('products' => ['You do not have any approved lots yet. Please purchase a lot.'])), 422);
            }
        }
        // Check for total left over volume
        /* We are turning this check off at the moment, this should not restrict the stock from coming in for now
        if($product_total_volume > $left_volume){
            if(request()->wantsJson()) {
                return response(json_encode(array('products' => ['You only have ' . Utilities::convertCentimeterCubeToMeterCube($left_volume) . 'm³ of space left but you are trying to fit in ' . Utilities::convertCentimeterCubeToMeterCube($product_total_volume) . 'm³. Please purchase more lots.'])), 422);
            }
            return redirect()->back()->withErrors("You have exceeded your lot limit.");
        }
        */
        // Check for days before order
        $settings = Settings::all();
        $days_before_order = $settings->filter(function($value){ return $value->setting_key == 'days_before_order'; })->first()->setting_value;
        if( $days_before_order != 0 && $compare->diffInDays($now) < $days_before_order ){
            if(request()->wantsJson()) {
                return response(json_encode(array('arrival_date' => ['Inbound must be created '.$days_before_order.' day(s) before.'])), 422);
            }
            return redirect()->back()->withErrors("Inbound must be created before ".$days_before_order." days.");
        }

        //Check whether selected a branch
        if($request->selectedBranch == null) {
            if(request()->wantsJson()) {
                return response(json_encode(array('selectedBranch' => ['Inbound must have a branch'])), 422);
            }
            return redirect()->back()->withErrors("Inbound must have a branch.");
        }
        else {
            $lots = Lot::where('branch_id', $request->selectedBranch)->get();
            if(!$lots) {
                if(request()->wantsJson()) {
                    return response(json_encode(array('selectedBranch' => ['Lot for this branch does not exist. Please select another branch.'])), 422);
                }
                return redirect()->back()->withErrors("Lot for this branch does not exist. Please select another branch.");
            }
        }
        // Everything is ok, create a new inbound
        $inbound = new Inbound();
        $inbound->user_id = $auth->id;
        $inbound->arrival_date = $request->arrival_date;
        $inbound->total_carton = $request->total_carton;
        $inbound->branch_id = $request->selectedBranch;
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
            $lots = $theproduct->lots()->get();
            foreach($lots as $lot)
            {
                if($product["quantity"] > 0) {
                    $quantityIntoLot = $this->calculateQuantity($lot->left_volume, $product["singleVolume"], $product["quantity"]);
                    if($quantityIntoLot > 0){
                        
                        $products[$key]["volume"] = $product["volume"] - ( $product["singleVolume"] * $quantityIntoLot );
                        $products[$key]["quantity"] = $product["quantity"] - $quantityIntoLot;
                        $product["quantity"] = $products[$key]["quantity"];
                        $this->attachLot($inbound->id, $key, $lot, $product["expiry_date"], $quantityIntoLot);
                        $lot->save();
                        $new_quantity = $lot->pivot->incoming_quantity + $quantityIntoLot;
                        $lot->products()->updateExistingPivot($key, ["incoming_quantity" => $new_quantity]);
                    }
                }
                

            $lot->propagate_left_volume();
            }
        }
        // Assign products to user lots
        $user_lots_with_enough_volume = $auth->lots()->get();
        foreach($user_lots_with_enough_volume as $lot_key => $lot ){
            $lot_products = [];
            foreach($products as $key => $product){
                if($product["quantity"] > 0) {
                    // If there are still volume needed to be assigned
                    $quantityIntoLot = $this->calculateQuantity($lot->left_volume, $product["singleVolume"], $product["quantity"]);

                    if($lot_key + 1 == $user_lots_with_enough_volume->count() )
                        $quantityIntoLot = $product["quantity"];

                    //dd($quantityIntoLot);
                    if($quantityIntoLot > 0){
                        $new_incoming_quantity = $quantityIntoLot;
                        $existing_lot_product = $lot->products()->where('id', $key)->first();
                        if($existing_lot_product)
                        {
                            $new_incoming_quantity += $existing_lot_product->pivot->incoming_quantity;
                            $existing_lot_product->lots()->updateExistingPivot($lot->id, ["incoming_quantity" => $new_incoming_quantity]);
                        }
                        else
                        {
                            $lot_products[$key]['incoming_quantity'] = $quantityIntoLot;
                        }

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
        $inbound->notify(new InboundCreatedNotification($inbound), new AdminInboundCreatedNotification());

        event(new EventTrigger('inbound'));

        return ['message' => "Inbound order created"];
    }

    public function calculateQuantity($volume, $singleVolume, $quantity){
        $quantityIntoLot = floor($volume / $singleVolume);
        return min($quantityIntoLot, $quantity);
    }

    public static function CALCULATE_QUANTITY($volume, $singleVolume, $quantity)
    {   
        $quantityIntoLot = floor($volume / $singleVolume);
        return min($quantityIntoLot, $quantity);
    }

    public function attachLot($inbound, $product, $lot, $expiry_date, $quantity) {
        // Attach lot to inbound product
        $inbound_product = InboundProduct::where('inbound_id', $inbound)->where('product_id', $product)->first();
        $inbound_product->lots()->attach($lot, ['quantity_received' => $quantity, 'quantity_original' => $quantity, 'expiry_date' => $expiry_date ? $expiry_date : null]);

        return $inbound_product;
    }

    public static function ATTACH_LOT($inbound, $product, $lot, $expiry_date, $quantity)
    {
        // Used by excel import, putting it here as it is easier to manage in the future
        $inbound_product = InboundProduct::where('inbound_id', $inbound)->where('product_id', $product)->first();
        $inbound_product->lots()->attach($lot, ['quantity_received' => $quantity, 'quantity_original' => $quantity, 'expiry_date' => $expiry_date ? $expiry_date : null]);

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

            /*foreach($product->lots as $lot)
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
            }*/
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
                        'quantity_original' => $lot->original_quantity,
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

    public function adjustProduct()
    {
        $completed_inbounds = DB::table('inbounds')
        ->select('inbound_product.product_id', 'inbound_product_lot.quantity_received', 'inbound_product_lot.lot_id')
        ->join('inbound_product','inbounds.id','=','inbound_product.inbound_id' )
        ->join('inbound_product_lot','inbound_product.id','=','inbound_product_lot.inbound_product_id' )
        ->where('inbounds.process_status', 'completed')
        ->get();
        
        foreach($completed_inbounds as $key => $completed_inbound){
            $lot = Lot::where('id',$completed_inbound->lot_id)->first();
            if($lot->products->where('id', $completed_inbound->product_id)->isEmpty()){
                $lot->products()->attach($completed_inbound->product_id, ['quantity' => $completed_inbound->quantity_received]);
            } else {
                foreach($lot->products->where('id', $completed_inbound->product_id) as $product){
                    $new_quantity = $product->pivot->quantity + $completed_inbound->quantity_received;
                    $lot->products()->updateExistingPivot($completed_inbound->product_id, ["quantity" => $new_quantity]);
                }
            }
        }

        $awaiting_inbounds = DB::table('inbounds')
        ->select('inbound_product.product_id', 'inbound_product_lot.quantity_received', 'inbound_product_lot.lot_id')
        ->join('inbound_product','inbounds.id','=','inbound_product.inbound_id' )
        ->join('inbound_product_lot','inbound_product.id','=','inbound_product_lot.inbound_product_id' )
        ->where('inbounds.process_status', 'awaiting_arrival')
        ->get();

        foreach($awaiting_inbounds as $key => $awaiting_inbound){
            $lot = Lot::where('id',$awaiting_inbound->lot_id)->first();
            if($lot->products->where('id', $awaiting_inbound->product_id)->isEmpty()){
                $lot->products()->attach($awaiting_inbound->product_id, ['incoming_quantity' => $awaiting_inbound->quantity_received]);
            } else {
                foreach($lot->products->where('id', $awaiting_inbound->product_id) as $product){
                    $new_quantity = $product->pivot->incoming_quantity + $awaiting_inbound->quantity_received;
                    $lot->products()->updateExistingPivot($awaiting_inbound->product_id, ["incoming_quantity" => $new_quantity]);
                }
            }
        }

        $processing_inbounds = DB::table('inbounds')
        ->select('inbound_product.product_id', 'inbound_product_lot.quantity_received', 'inbound_product_lot.lot_id')
        ->join('inbound_product','inbounds.id','=','inbound_product.inbound_id' )
        ->join('inbound_product_lot','inbound_product.id','=','inbound_product_lot.inbound_product_id' )
        ->where('inbounds.process_status', 'processing')
        ->get();

        foreach($processing_inbounds as $key => $processing_inbound){
            $lot = Lot::where('id',$processing_inbound->lot_id)->first();
            if($lot->products->where('id', $processing_inbound->product_id)->isEmpty()){
                $lot->products()->attach($processing_inbound->product_id, ['incoming_quantity' => $processing_inbound->quantity_received]);
            } else {
                foreach($lot->products->where('id', $processing_inbound->product_id) as $product){
                    $new_quantity = $product->pivot->incoming_quantity + $processing_inbound->quantity_received;
                    $lot->products()->updateExistingPivot($processing_inbound->product_id, ["incoming_quantity" => $new_quantity]);
                }
            }
        }

        $pending_outbounds = DB::table('outbounds')
        ->select('outbound_product.product_id', 'outbound_product.quantity', 'outbound_product.lot_id')
        ->join('outbound_product','outbounds.id','=','outbound_product.outbound_id' )
        ->where('outbounds.process_status', 'pending')
        ->get();

        foreach($pending_outbounds as $key => $pending_outbound){
            $lot = Lot::where('id',$pending_outbound->lot_id)->first();                
            foreach($lot->products->where('id', $pending_outbound->product_id) as $product){
                    $new_quantity = $product->pivot->outgoing_product + $pending_outbound->quantity;
                    $lot->products()->updateExistingPivot($pending_outbound->product_id, ["outgoing_product" => $new_quantity]);
            }
        }

        $completed_outbounds = DB::table('outbounds')
        ->select('outbound_product.product_id', 'outbound_product.quantity', 'outbound_product.lot_id')
        ->join('outbound_product','outbounds.id','=','outbound_product.outbound_id' )
        ->where('outbounds.process_status', 'completed')
        ->get();

        foreach($completed_outbounds as $key => $completed_outbound){
            $lot = Lot::where('id',$completed_outbound->lot_id)->first();
            foreach($lot->products->where('id', $completed_outbound->product_id) as $product){
                $new_quantity = $product->pivot->quantity - $completed_outbound->quantity;
                $lot->products()->updateExistingPivot($completed_outbound->product_id, ["quantity" => $new_quantity]);
            }
        }

        $processing_outbounds = DB::table('outbounds')
        ->select('outbound_product.product_id', 'outbound_product.quantity', 'outbound_product.lot_id')
        ->join('outbound_product','outbounds.id','=','outbound_product.outbound_id' )
        ->where('outbounds.process_status', 'processing')
        ->get();

        foreach($processing_outbounds as $key => $processing_outbound){
            $lot = Lot::where('id',$processing_outbound->lot_id)->first();
            foreach($lot->products->where('id', $processing_outbound->product_id) as $product){
                $new_quantity = $product->pivot->outgoing_product + $processing_outbound->quantity;
                $lot->products()->updateExistingPivot($processing_outbound->product_id, ["outgoing_product" => $new_quantity]);
            }
        }
    }
}