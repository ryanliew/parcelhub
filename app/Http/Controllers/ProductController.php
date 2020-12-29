<?php

namespace App\Http\Controllers;

use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ProductController extends Controller
{
    protected $rules = [
        'name' => 'required',
        'height' => 'required',
        'length' => 'required',
        'width' => 'required',
        'sku' => 'required',
        'user_id' => 'required',
        'picture' => 'required|max:20000'
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
     * Return the view which contains the vue page for product bulk upload wizard
     * @return \Illuminate\Http\Response
     */
    public function page_bulk()
    {
        return view('product.excel');
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

            if($user->hasRole('subuser'))
            {
                $user = $user->parent;
            }

            if($user->hasRole('admin'))
                return Controller::VueTableListResult(Product::with(["inbounds", "lots", "outbounds", "adjustments"])->select('products.picture as picture',
                                        'products.sku as sku',
                                        'products.id as id',
                                        'products.name as product_name',
                                        'products.is_dangerous as is_dangerous',
                                        'products.is_fragile as is_fragile',
                                        'products.width as width',
                                        'products.height as height',
                                        'products.length as length',
                                        'users.name as user_name',
                                        'users.id as user_id',
                                        'products.trash_hole as threshold'
                                    )
                                ->leftJoin('users', 'products.user_id', '=', 'users.id')
                                ->where('status', 'true'));
            else
                return Controller::VueTableListResult($user->products()->with(["inbounds", "lots", "outbounds", "adjustments"])
                                                                                ->select('products.picture as picture',
                                                                                        'products.sku as sku',
                                                                                        'products.id as id',
                                                                                        'products.name as product_name',
                                                                                        'products.trash_hole as trashole',
                                                                                        'products.is_dangerous as is_dangerous',
                                                                                        'products.is_fragile as is_fragile',
                                                                                        'products.width as width',
                                                                                        'products.height as height',
                                                                                        'products.length as length',
                                                                                        'users.name as user_name',
                                                                                        'users.id as user_id',
                                                                                        'products.trash_hole as threshold'
                                                                                    )
                                                                                ->leftJoin('users', 'products.user_id', '=', 'users.id')
                                                                                ->where('status', 'true')
                                                                            );
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
        $query = Product::query();
        if(!auth()->user()->hasRole('admin'))
        {
            $query = auth()->user()->products();
        }
        return $query->with(["inbounds", "lots", "outbounds"])->where('status', 'true')->get();
    }

    public function adminProduct($id)
    {
        return User::find($id)->products()->with(["inbounds", "lots", "outbounds"])->where('status', 'true')->get();
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
        //$auth_id = auth()->user()->id;
        $auth_id = $request->user_id;
        $product = new product;
        $product->name = $request->name;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->sku = $request->sku;
        $product->is_fragile = $request->has('is_fragile');
        $product->is_dangerous = $request->has('is_dangerous');  
        $product->user_id = $auth_id;
        if($request->trashole){
            $product->trash_hole = $request->trashole;
        }
        $product->status = 'true';
        

        if(Input::hasFile('picture')){
            $file = Input::file('picture');
            $path = $file->hashName('public');
            $image = Image::make($file);

            $image->resize(300,null, function($constraint) {
                $constraint->aspectRatio();
            });
            Storage::put($path, (string) $image->encode());

            // $product->picture = $file->store('public');
            $product->picture = $path;
        }
        $product->save();

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
        if($request->threshold){
            $product->trash_hole = $request->threshold;     
        }
        $product->sku = $request->sku;
        if(Input::hasFile('picture')){
            $file = Input::file('picture');
            $path = $file->hashName('public');
            $image = Image::make($file);

            $image->resize(300,null, function($constraint) {
                $constraint->aspectRatio();
            });
            Storage::put($path, (string) $image->encode());

            // $product->picture = $file->store('public');
            $product->picture = $path;
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
    public function destroy(Product $product)
    {
        if($product->total_quantity > 0)
            return ['message' => 'Product with stock shall not be deleted'];


        $product->status = "false";
        $product->save();

        return ['message' => "Product deleted"];

        return redirect()->back()->withSuccess($product->name . ' deleted successfully.');
    }

    public function reconcile(Product $product)
    {
        return $product->reconcile_quantity();
    }
}
