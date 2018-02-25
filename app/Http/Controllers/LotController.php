<?php

namespace App\Http\Controllers;

use App\Lot;
use App\Category;
use App\Payment;
use App\Settings;
use Illuminate\Http\Request;

class LotController extends Controller
{
    protected $rules = [
        'name' => 'required',
        'volume' => 'required',
        'price' => 'required',
    ];

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
        if(\Entrust::hasRole('admin')) {
            if(request()->wantsJson() )
            {
                return Controller::VueTableListResult(
                    Lot::with(['products'])
                        ->select('lots.id as id', 
                                'lots.name as name', 
                                'lots.left_volume as left_volume',
                                'categories.name as category_name', 
                                'categories.id as category_id',
                                'categories.volume as category_volume',
                                'lots.volume as volume', 
                                'lots.price as price',
                                'users.name as user_name',
                                'users.id as user_id')
                        ->join('categories', 'categories.id', '=', 'category_id')
                        ->leftJoin('users', 'users.id', '=', 'user_id')
                    );
            }

            $categories = category::where('status', 'true')->get();
            
            $lots = lot::where('status', 'true')->get();

            return view('lot.admin')->with(compact('categories', 'lots'));
        }
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

        $lot = new lot;
        $lot->name = $request->name;
        $lot->volume = $request->volume;
        $lot->left_volume = $request->volume;
        $lot->category_id = $request->category;
        $lot->price = $request->price;
        $lot->status = "false";
        $lot->rental_duration = Settings::rentalDuration();
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
        $this->validate($request, $this->rules);
        
        $lot = lot::find($request->id);
        $lot->name = $request->name;
        $lot->category_id = $request->category;
        $lot->volume = $request->volume;
        $lot->left_volume = $request->volume;
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
        $lot->save();

        return ['message' => 'Lot reassigned successfully.'];
    }
}
