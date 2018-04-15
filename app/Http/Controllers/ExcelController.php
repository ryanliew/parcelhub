<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Product;
use Storage;

class ExcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('excel.index');
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
        $excelRows = Excel::load($request->file('file'))->toArray();
        foreach($excelRows as $excelRow){
            $product = Product::firstOrCreate(
                 ['sku' => $excelRow['sku']],
                 ['sku' => $excelRow['sku'],
                'name' => $excelRow['name'],
                'height' => $excelRow['heightcm'],
                'length' => $excelRow['lengthcm'],
                'width' => $excelRow['widthcm'],
                'is_dangerous' => $excelRow['dangerous'],
                'is_fragile' => $excelRow['fragile'],
                'trash_hole' => $excelRow['minstocklevel'],
                'user_id' => auth()->id()
            ]);
        }

        return ["message" => "Products uploaded successfully", "number" => sizeof($excelRows)];
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
    public function update(Request $request, $id)
    {
        //
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

    public function uploadPhotos(Request $request)
    {
        $filename = explode('.', $request->file->getClientOriginalName())[0];
        $product = Product::where('SKU', $filename)->first();
        if(!is_null($product)){
            $product->picture = $request->file->store('public');
            $product->save();
        }
    }

    public function download()
    {
        $path = storage_path('app\public\parcelhub_products_import.xlsx');

        return response()->download($path);
    }
}
