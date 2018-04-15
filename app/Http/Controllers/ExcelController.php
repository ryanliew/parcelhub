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
            $product = new product();
            $product->sku = $excelRow['sku'];
            $product->name = $excelRow['name'];
            $product->height = $excelRow['heightcm'];
            $product->length = $excelRow['lengthcm'];
            $product->width = $excelRow['widthcm'];
            $product->is_dangerous = $excelRow['dangerous'];
            $product->is_fragile = $excelRow['fragile'];
            $product->user_id = auth()->id();
            $product->save();
        }
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

    public function download()
    {
        $path = storage_path('\app\public\parcelhub.xlsx');

        return response()->download($path);
    }
}
