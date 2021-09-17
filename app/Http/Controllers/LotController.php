<?php

namespace App\Http\Controllers;

use App\Category;
use App\Lot;
use App\Payment;
use App\Product;
use App\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Settings;

class LotController extends Controller
{
    protected $rules = [
        'name' => 'required',
        'volume' => 'required',
        'price' => 'required',
        'category' => 'required'
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
     * Display the Vue page for lots
     * @return \Illuminate\Http\Response
     */
    public function page()
    {
        return view('lot.page');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        if($user->hasRole('subuser'))
        {
            $user = $user->parent;
        }
 
        if(request()->wantsJson() )
        {
            if($user->hasRole('superadmin')) {
                return Controller::VueTableListResult(
                    Lot::with(['products'])
                        ->select('lots.id as id', 
                                'lots.name as name', 
                                'lots.status as lot_status',
                                'categories.name as category_name', 
                                'categories.id as category_id',
                                'categories.volume as category_volume',
                                'categories.price as category_price',
                                'lots.volume as volume', 
                                'lots.price as price',
                                'lots.left_volume as left_volume',
                                'branches.name as branch_name',
                                'branches.code as branch_code',
                                'users.name as user_name',
                                'lots.expired_at as expired_at',
                                'users.id as user_id')
                        ->selectRaw('lots.volume - lots.left_volume as lot_usage')
                        ->join(env('DB2_DATBASE').'.branches as branches', 'branches.code', '=', 'branch_code')
                        ->join('categories', 'categories.id', '=', 'category_id')
                        ->leftJoin('users', 'users.id', '=', 'user_id')
                    );
            }
            else if($user->hasRole('admin')) {
                return Controller::VueTableListResult(
                    Lot::with(['products'])
                        ->select('lots.id as id', 
                        'lots.name as name', 
                        'lots.status as lot_status',
                        'categories.name as category_name', 
                        'categories.id as category_id',
                        'categories.volume as category_volume',
                        'categories.price as category_price',
                        'lots.volume as volume', 
                        'lots.price as price',
                        'lots.left_volume as left_volume',
                        'branches.name as branch_name',
                        'branches.code as branch_code',
                        'users.name as user_name',
                        'lots.expired_at as expired_at',
                        'users.id as user_id')
                        ->selectRaw('lots.volume - lots.left_volume as lot_usage')
                        ->leftJoin('users', 'users.id', '=', 'user_id')
                        ->join('categories', 'categories.id', '=', 'category_id')
                        ->join('parcelhub_center.branches', 'branches.code', '=', 'branch_code')
                        ->join('accessibilities', 'accessibilities.branch_code', '=', 'branches.code')
                        ->where('accessibilities.user_id', $user->id)
                );
            }
            else {
                return Controller::VueTableListResult(
                    $user->lots()->with(['products'])
                                ->select('lots.id as id', 
                                        'lots.name as name', 
                                        'lots.status as lot_status',
                                        'categories.name as category_name', 
                                        'categories.id as category_id',
                                        'categories.volume as category_volume',
                                        'categories.price as category_price',
                                        'lots.volume as volume', 
                                        'lots.left_volume as left_volume',
                                        'lots.price as price',
                                        'branches.name as branch_name',
                                        'users.name as user_name',
                                        'lots.expired_at as expired_at',
                                        'users.id as user_id')
                                ->selectRaw('lots.volume - lots.left_volume as lot_usage')
                                ->join('categories', 'categories.id', '=', 'category_id')
                                ->join('parcelhub_center.branches as branches', 'branches.code', '=', 'branch_code')
                                ->leftJoin('users', 'users.id', '=', 'user_id')
                    );
            }
        }

        $categories = category::where('status', 'true')->get();
        
        $lots = lot::where('status', 'approved')->get();
        
        return view('lot.admin')->with(compact('categories', 'lots'));
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
        if($request->name == null) {
            if(request()->wantsJson()) {
                return response(json_encode(array('name' => ['Please fill in the lot\'s name'])), 422);
            }
            return redirect()->back()->withErrors("Please fill in the lot\'s name");
        }
        if($request->category == null) {
            if(request()->wantsJson()) {
                return response(json_encode(array('category' => ['Please select a category!'])), 422);
            }
            return redirect()->back()->withErrors("Please select a category!");
        }

        if($request->selectedBranch == null) {
            if(request()->wantsJson()) {
                return response(json_encode(array('selectedBranch' => ['Please select a branch'])), 422);
            }
            return redirect()->back()->withErrors("Please select a branch!");
        }
        $settings = Settings::all();
        $rental_duration = $settings->filter(function($value){return $value->setting_key == 'rental_duration';})->first()->setting_value;
        $lot = new Lot;
        $lot->name = $request->name;
        $lot->volume = $request->volume;
        $lot->left_volume = $lot->volume;
        $lot->category_id = $request->category;
        $lot->branch_code = $request->selectedBranch;
        $lot->price = $request->price;
        $lot->status = "false";
        $lot->rental_duration = (int)$rental_duration;
        $lot->save();

        if(request()->wantsJson())
        {   
            return ["message" => $request->name . " created successfully."];
        }

        return redirect()->back()->withSuccess($request->name . " created successfully.");
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
        $this->validate($request, $this->rules, ['category.required' => 'The category field is required']);
        
        $lot = Lot::find($request->id);
        $lot->name = $request->name;
        $lot->category_id = $request->category;
        $lot->branch_code = $request->selectedBranch;
        $lot->volume = $request->volume;
        $lot->left_volume = Utilities::convertMeterCubeToCentimeterCube($request->volume);
        $lot->price = $request->price;
        $lot->save();

        if(request()->wantsJson())
        {   
            return ["message" => "Lot " . $lot->name . ' updated successfully.'];
        }

        return redirect()->back()->withSuccess($lot->name . ' updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lot = lot::find($id);

        $lotAssigned = $lot->products;
        
        if(!sizeof($lotAssigned) > 0){
            // not assigned then here
            $lot->status = "false";
            $lot->save();
            return redirect()->back()->withSuccess($lot->name . ' deleted successfully.');
        } else {
            // assigned then here
            return redirect()->back()->withErrors($lot->name . ' cannot be deleted because this lot is assigned to product.');
        }
    }

    /**
     * Reassign specified lot to another user
     * @param  Lot    $lot 
     * @return \Illuminate\Http\Response
     */
    public function assign(Request $request, Lot $lot)
    {
        $this->validate($request, ['user_id' => 'required']);

        if( $lot->user_id !== $request->user_id
            && $lot->products()->count() > 0 )
        {
            return response(json_encode(array('user_id' => ['There are still products of the previous user, reassignment is not allowed.'])), 422);
        }

        $lot->user_id = $request->user_id;
        $lot->status = 'approved';
        $lot->expired_at = null;
        $lot->save();

        return ['message' => 'Lot reassigned successfully.'];
    }

    /**
     * Remove the owner from the specified lot
     * @param  Lot    $lot 
     * @return \Illuminate\Http\Response
     */
    public function unassign(Request $request, Lot $lot)
    {
        if( $lot->user_id !== $request->user_id
            && $lot->products()->count() > 0 )
        {
            return response(json_encode(array('user_id' => ['There are still products of the previous user, unassignment is not allowed.'])), 422);
        }

        $lot->user()->dissociate();
        $lot->expired_at = null;
        $lot->status = 'false';
        $lot->save();

        return ['message' => 'Lot unassigned successfully.'];
    }

    public function editStock(Request $request)
    {
        $lot_products = json_decode($request['lot_products'], true);
        $adjustments = [];

        $the_product = Product::find(collect($lot_products)->first()['product_id']);
    
        foreach($lot_products as $lotproduct)
        {
            if($lotproduct['remark'] !== '')
            {
                $lot = Lot::find($lotproduct['lot_id']);
                $original_quantity = 0;
                $product = $lot->products()->where('product_id', $lotproduct['product_id'])->first();
                if($product) {
                    $original_quantity = $product->pivot->quantity;
                    $lot->products()->updateExistingPivot($lotproduct['product_id'], 
                                                            ['quantity' => $lotproduct['quantity']]);
                }
                else {
                    $lot->products()->attach($lotproduct['product_id'], ['quantity' => $lotproduct['quantity']]);
                }

                array_push($adjustments, [
                    'lot_id' => $lot->id,
                    'user_id' => auth()->id(),
                    'original_quantity' => $original_quantity,
                    'new_quantity' => $lotproduct['quantity'],
                    'remark' => $lotproduct['remark']
                ]);

                $lot->propagate_left_volume();
                
            }
        }
        
        $the_product->adjustments()->createMany($adjustments);

        return ['message' => 'Stock updated successfully'];
    }
}
