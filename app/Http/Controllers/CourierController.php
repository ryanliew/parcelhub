<?php

namespace App\Http\Controllers;

use App\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $couriers = courier::where('status', 'true')->get();

        return view('courier.index')->with('couriers', $couriers);
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
        $courier = new courier;
        $courier->name = $request->name;
        $courier->status = 'true';
        $courier->save();

        return redirect()->back()->withSuccess($courier->name . " created successfully.");
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
        $courier = courier::find($request->id);
        $courier->name = $request->name;
        $courier->save();

        return redirect()->back()->withSuccess($courier->name . ' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $courier = courier::find($id);
        $courierAssigned = $courier->outbound;
        
        if(!sizeof($courierAssigned) > 0){
            // not assigned then here
            $courier->status = "false";
            $courier->save();
            return redirect()->back()->withSuccess($courier->name . ' deleted successfully.');
        } else {
            // assigned then here
            return redirect()->back()->withErrors($courier->name . ' cannot be deleted because this courier is assigned to outbound.');
        }
    }
}
