<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProductController extends Controller
{
    /**
     * Return the view which contains the vue page for product
     * @return \Illuminate\Http\Response
     */
    public function page()
    {
        return view('product.page');
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
            return Controller::VueTableListResult(product::where('status', 'true'));
        }

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
        $validatedData = $request->validate([
            'name' => 'required',
            'height' => 'required',
            'length' => 'required',
            'width' => 'required',
            'sku' => 'required',
        ]);

        $auth_id = auth()->user()->id;
        $product = new product;
        $product->name = $request->name;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->sku = $request->sku;  
        $product->user_id = $auth_id;      
        $product->status = 'true';
        $product->save();

        if(Input::hasFile('picture')){
            $file = Input::file('picture');
            $pictureNames = explode(".", $file->getClientOriginalName());
            $file->move('images', $auth_id.$pictureNames[0].$product->id.".JPG");
            $product->picture = $auth_id.$pictureNames[0].$product->id.".JPG";
            $product->save();
        }

        if(request()->wantsJson())
        {   
            return ["message" => $product->name . " created successfully."];
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
        $validatedData = $request->validate([
            'name' => 'required',
            'height' => 'required',
            'length' => 'required',
            'width' => 'required',
            'sku' => 'required',
        ]);
        
        $auth_id = auth()->user()->id;
        $product = product::find($request->id);
        $product->name = $request->name;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->sku = $request->sku;
        if(Input::hasFile('picture')){
            $file = Input::file('picture');
            $pictureNames = explode(".", $file->getClientOriginalName());
            $file->move('images', $auth_id.$pictureNames[0].$product->id.".JPG");
            $product->picture = $auth_id.$pictureNames[0].$product->id.".JPG";
        }
        $product->save();

        if(request()->wantsJson())
        {   
            return ["message" => $product->name . ' updated successfully.'];
        }

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
