<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = product::where('status', 'true')->get();

        return view('product.index')->with('products', $products);
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
        // waiting for auth id to be completed
        $product = new product;
        $product->lot_id = 1;
        $product->name = $request->name;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->sku = $request->sku;        
        $product->status = 'true';
        $product->save();

        if(Input::hasFile('picture')){
            $file = Input::file('picture');
            $pictureNames = explode(".", $file->getClientOriginalName());
            $file->move('images', "1".$pictureNames[0].$product->id.".JPG");
            $product->picture = "1".$pictureNames[0].$product->id.".JPG";
            $product->save();
        }
        

        return redirect()->back()->withSuccess($product->name . " created successfully.");
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
        $product = product::find($request->id);
        $product->name = $request->name;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->sku = $request->sku;
        if(Input::hasFile('picture')){
            $file = Input::file('picture');
            $pictureNames = explode(".", $file->getClientOriginalName());
            $file->move('images', "1".$pictureNames[0].$product->id.".JPG");
            $product->picture = "1".$pictureNames[0].$product->id.".JPG";
        }
        $product->save();

        return redirect()->back()->withSuccess($product->name . ' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = product::find($id);
        $product->status = "false";
        $product->save();

        return redirect()->back()->withSuccess($product->name . ' deleted successfully.');
    }
}
