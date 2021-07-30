<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Customer;
use App\Events\EventTrigger;
use App\Http\Controllers\InboundController;
use App\Inbound;
use App\Notifications\Admin\InboundCreatedNotification;
use App\Notifications\Admin\OutboundCreatedNotification;
use App\Outbound;
use App\Product;
use App\User;
use App\Utilities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Imports\ReportsImport;
use App\Imports\OutboundImport;
use App\Imports\InboundImport;
use Intervention\Image\ImageManagerStatic as Image;
use Maatwebsite\Excel\Facades\Excel;
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

        $this->validate($request, [
            'file' => 'required'
        ]);
        $excelRows = Excel::import(new ReportsImport, request()->file('file'));

        $count = Product::where("updated_at", '>=' , now()->subMinutes(3))->count();

        return ["message" => "Products uploaded successfully", "number" => $count];
    }

    public function processOutbound(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);
        try{
            Excel::import(new OutboundImport, request()->file('file'));
        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = collect($e->failures());
            $error_message = $failures->first()->errors()[0];
            $full_error_messages = $error_message . ' at row '. $failures->first()->row(). '.';

            return response(json_encode(array('overall' => [$full_error_messages])), 422);
        }

        $count = Outbound::where("updated_at", '>=' , now()->subMinutes(3))->count();

        User::superadmin()->first()->notify(new OutboundCreatedNotification());
        event(new EventTrigger('outbound'));
        
        return response()->json(['message' => $count . ' outbound orders created successfully']); 

    }

    public function processInbound(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);

        try{
            Excel::import(new InboundImport, request()->file('file'));
        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = collect($e->failures());

            return response(json_encode(array('overall' => [$failures->first()->errors()[0]])), 422);
        }

        $count = Inbound::where("updated_at", '>=' , now()->subMinutes(3))->count();

        if($count > 0)
        {
            $message = $count . ' inbound orders created successfully';
            User::superadmin()->first()->notify(new InboundCreatedNotification());
            event(new EventTrigger('inbound'));
        }
        else
        {
            $message = "The system failed to read the data. Kindly refer to the required example in the previous step and check the date format.";
        }

        return response()->json(['message' => $message]); 


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
        $product = Product::where('sku', $filename)
                        ->where('status', 'true')
                        ->where('user_id', auth()->id())
                        ->first();         

        if(!is_null($product)){
            $file = $request->file;
            $path = $file->hashName('public');

            $image = Image::make($file);
            $image->resize(300,null, function($constraint) {
                $constraint->aspectRatio();
            });
             Storage::put($path, (string) $image->encode());
             $product->picture = $path;

            //$product->picture = $request->file->store('public');
            $product->save();
        }
    }

    public function download()
    {
        $path = storage_path('app/public/parcelhub_products_import.xlsx');

        return response()->download($path);
    }

    public function downloadOutbound()
    {
        $path = storage_path('app/public/parcelhub_outbounds_import.xlsx');

        return response()->download($path);
    }

    public function downloadInbound()
    {
        $path = storage_path('app/public/parcelhub_inbound_import.xlsx');

        return response()->download($path);
    }
}
