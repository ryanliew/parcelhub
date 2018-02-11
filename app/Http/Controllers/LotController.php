<?php

namespace App\Http\Controllers;

use App\Lot;
use App\Category;
use App\Payment;
use Illuminate\Http\Request;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Entrust::hasRole('admin')) {

            $lots = Lot::all();
            return view('lot.admin')->with('lots', $lots);
        }

        return view('lot.user');
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
        $lot = new lot;
        $lot->name = $request->name;
        $lot->volume = $request->volume;
        $lot->category_id = $request->category;
        $lot->status = "true";
        $lot->save();

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
        $lot = lot::find($request->id);
        $lot->name = $request->name;
        $lot->category_id = $request->category;
        $lot->volume = $request->volume;
        $lot->save();

        return redirect()->back()->withSuccess($lot->name . ' updated successfully.');
    }

    public function purchase(Request $request) {

        $this->validate($request, [
            'lots.*' => 'required',
            'lots.*.name' => 'required',
            'lots.*.categories' => 'required',
            'lots.*.volume' => 'required',
        ]);

        $lots = $request->input('lots');

        foreach($lots as $l) {
            $lot = new Lot();
            $lot->name = $l['name'];
            $lot->user_id = auth()->id();
            $lot->category_id = $l['categories'];
            $lot->volume = $l['volume'];
            $lot->status = "false";
            $lot->save();
        }

        return redirect()->back();
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
}
