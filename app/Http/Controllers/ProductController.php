<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProductController extends Controller
{
    protected $rules = [
        'name' => 'required',
        'height' => 'required',
        'length' => 'required',
        'width' => 'required',
        'sku' => 'required',
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
            $user = auth()->user();
            if($user->hasRole('admin'))
                return Controller::VueTableListResult(Product::with(["inbounds", "lots", "outbounds"])->select('products.picture as picture',
                                        'products.sku as sku',
                                        'products.id as id',
                                        'products.name as product_name',
                                        'products.is_dangerous as is_dangerous',
                                        'products.is_fragile as is_fragile',
                                        'products.width as width',
                                        'products.height as height',
                                        'products.length as length',
                                        'users.name as user_name'
                                    )
                                ->leftJoin('users', 'products.user_id', '=', 'users.id'));
            else
                return Controller::VueTableListResult(auth()->user()->products()->with(["inbounds", "lots", "outbounds"])->select('products.picture as picture',
                                        'products.sku as sku',
                                        'products.id as id',
                                        'products.name as product_name',
                                        'products.is_dangerous as is_dangerous',
                                        'products.is_fragile as is_fragile',
                                        'products.width as width',
                                        'products.height as height',
                                        'products.length as length',
                                        'users.name as user_name'
                                    )
                                ->leftJoin('users', 'products.user_id', '=', 'users.id'));
        }
        

        $products = product::where('status', 'true')->get();

        return view('product.index')->with('products', $products);
    }

    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function selector()
    {
        return Controller::VueTableListResult(auth()->user()->products()->with(["inbounds", "lots", "outbounds"]));
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

        $auth_id = auth()->user()->id;
        $product = new product;
        $product->name = $request->name;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->sku = $request->sku;
        $product->is_fragile = $request->has('is_fragile');
        $product->is_dangerous = $request->has('is_dangerous');  
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
        $this->validate($request, $this->rules);

        $auth_id = auth()->user()->id;
        $product = product::find($request->id);
        $product->name = $request->name;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->is_fragile = $request->has('is_fragile');
        $product->is_dangerous = $request->has('is_dangerous');  
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
