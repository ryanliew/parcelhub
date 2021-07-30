<?php

namespace App\Http\Controllers;

use App\User;
use App\Courier;
use App\Lot;
use App\Branch;
use App\Notifications\OutboundCreatedNotification;
use App\Notifications\Admin\OutboundCreatedNotification as AdminOutboundCreatedNotification;
use App\Outbound;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PDF;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Events\EventTrigger;

class OutboundController extends Controller
{
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
     * Return the page view for outbounds
     * @return \Illuminate\Http\Response
     */
    public function page()
    {
        return view('outbound.page');
    }

    public function page_excel()
    {
        return view('outbound.excel');
    }

    /**
     * Return the bulk tracking insert page view for outbounds
     * @return \Illuminate\Http\Response
     */
    public function page_bulk()
    {
        return view('outbound.page-bulk');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->wantsJson()) {
            $user = auth()->user();

            if($user->hasRole('subuser'))
            {
                $user = $user->parent;
            }

            if($user->hasRole('superadmin'))
                
                return Controller::VueTableListResult( Outbound::with('tracking_numbers')
                                                                ->select('outbounds.id as id',
                                                                    'amount_insured',
                                                                    'process_status',
                                                                    'couriers.name as courier',
                                                                    'branches.codename',
                                                                    'branches.branch_name',
                                                                    'outbounds.created_at',
                                                                    'outbounds.recipient_name',
                                                                    'outbounds.recipient_address',
                                                                    'outbounds.recipient_address_2',
                                                                    'outbounds.recipient_phone',
                                                                    'outbounds.recipient_state',
                                                                    'outbounds.recipient_country',
                                                                    'outbounds.recipient_postcode',
                                                                    'users.name as customer',
                                                                    'outbounds.type as type'
                                                                    )
                                                                ->where('outbounds.type', 'outbound')
                                                                ->leftJoin('couriers', 'courier_id', '=', 'couriers.id')
                                                                ->join('branches', 'outbounds.branch_id' , '=', 'branches.id')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
                                                                ->orderBy('outbounds.created_at', 'desc') );
            elseif($user->hasRole('admin')) 
                return Controller::VueTableListResult( Outbound::with('tracking_numbers')
                    ->select('outbounds.id as id',
                        'amount_insured',
                        'process_status',
                        'couriers.name as courier',
                        'branches.codename',
                        'branches.branch_name',
                        'outbounds.created_at',
                        'outbounds.recipient_name',
                        'outbounds.recipient_address',
                        'outbounds.recipient_address_2',
                        'outbounds.recipient_phone',
                        'outbounds.recipient_state',
                        'outbounds.recipient_country',
                        'outbounds.recipient_postcode',
                        'users.name as customer',
                        'outbounds.type as type'
                        )
                    ->where('outbounds.type', 'outbound')
                    ->leftJoin('couriers', 'courier_id', '=', 'couriers.id')
                    ->leftJoin('users', 'user_id', '=', 'users.id')
                    ->join('branches', 'branches.id', '=', 'branch_id')
                    ->join('accessibilities', 'accessibilities.branch_id', '=', 'branches.id')
                    ->where('accessibilities.user_id', $user->id)
                    ->orderBy('outbounds.created_at', 'desc') );
            else

                $branches = Branch::select('branches.id')
                        ->leftJoin('lots' , 'lots.branch_id', '=', 'branches.id')
                        ->where('lots.user_id', $user->id)
                        ->get();
                $array_branch = [];
                foreach($branches as $branch) {
                    array_push($array_branch, $branch->id);
                }
                return Controller::VueTableListResult( $user->outbounds()
                                                            ->with('tracking_numbers')
                                                            ->select('outbounds.id as id',
                                                                    'amount_insured',
                                                                    'process_status',
                                                                    'couriers.name as courier',
                                                                    'branches.codename',
                                                                    'branches.branch_name',
                                                                    'outbounds.created_at',
                                                                    'outbounds.recipient_name',
                                                                    'outbounds.recipient_address',
                                                                    'outbounds.recipient_address_2',
                                                                    'outbounds.recipient_phone',
                                                                    'outbounds.recipient_state',
                                                                    'outbounds.recipient_country',
                                                                    'outbounds.recipient_postcode',
                                                                    'outbounds.type as type'
                                                                    )
                                                            ->where('outbounds.type', 'outbound')
                                                            ->whereIn('outbounds.branch_id', $array_branch)
                                                            ->join('branches', 'outbounds.branch_id' , '=', 'branches.id')
                                                            ->leftJoin('couriers', 'courier_id', '=', 'couriers.id')
                                                            ->orderBy('outbounds.created_at', 'desc') );

        }

        if($user->hasRole('admin')) {

            $outbounds = Outbound::processing()->get();
            return view('outbound.admin')->with('outbounds', $outbounds);

        } else {

            $products = Product::with('lots')->where('user_id', auth()->id())->get();

            $couriers = Courier::all();

            return view('outbound.user')->with(compact('products', 'couriers'));
        }
    }

    public function indexPending()
    {
       return Controller::VueTableListResult( Outbound::with('tracking_numbers')
                                                        ->select('outbounds.id as id',
                                                                    'amount_insured',
                                                                    'process_status',
                                                                    'couriers.name as courier',
                                                                    'outbounds.created_at',
                                                                    'outbounds.recipient_name',
                                                                    'outbounds.recipient_address',
                                                                    'outbounds.recipient_address_2',
                                                                    'outbounds.recipient_phone',
                                                                    'outbounds.recipient_state',
                                                                    'outbounds.recipient_country',
                                                                    'outbounds.recipient_postcode',
                                                                    'users.name as customer',
                                                                    'outbounds.type as type'
                                                                    )
                                                                ->leftJoin('couriers', 'courier_id', '=', 'couriers.id')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
                                                                ->where('outbounds.type', 'outbound')
                                                                ->where('outbounds.process_status', '<>', 'completed')
                                                                ->where('outbounds.process_status', '<>', 'canceled')
                                                                ->orderBy('outbounds.created_at', 'desc'));
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

    public function packingList($id)
    {
        $outbound = Outbound::with('products.lots')->find($id);

        $pdf = PDF::loadView('outbound.packing', compact('outbound'));

        $filename = Outbound::prefix() . $outbound->id . '-packing.pdf';

        // return view("outbound.packing", compact('outbound'));
        return $pdf->setPaper('A4')->download($filename);
    }

    public function report($id)
    {
        $outbound = Outbound::with('products')->find($id);
        $path = storage_path();
        $pdf = PDF::loadView('outbound.report', compact(['outbound', 'path']));
        $filename = Outbound::prefix() . $outbound->id . '.pdf';
        $mime = "";

        if($outbound->payer_gst_vat != null){
            $courier = Courier::find($outbound->courier_id);
            $auth = auth()->user();
            $totalQuantity = $outbound->totalQuantity();
            $totalPrice = $outbound->totalValue();

            $pdf = PDF::loadView('outbound.proforma', compact(['outbound', 'courier', 'auth', 'totalQuantity', 'totalPrice']));

            return $pdf->setPaper('A4')->download('outbound-proforma-invoice-'.$id.'.pdf');
        }

        if($outbound->invoice_slip) {
            
            $mime = Storage::mimeType($outbound->invoice_slip);
            $extension = explode("/", $mime);
            $path = storage_path('app/' . $outbound->invoice_slip);

            if($mime == "application/pdf")
            {
                $outbound_pdf = storage_path('outbound-report.pdf');
                $pdf->setPaper('A4')->save($outbound_pdf);
                
                $pdf = new \LynX39\LaraPdfMerger\PdfManage;

                $pdf->addPDF($path, 'all');
                $pdf->addPDF($outbound_pdf, 'all');
                $pdf->merge('download', $filename, 'P');
            }
            else
            {
                return $pdf->setPaper('A4')->download($filename);
            }
        }
        else
        {
            return $pdf->setPaper('A4')->download($filename);
        }
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
            'recipient_name' => 'required',
            'recipient_address' => 'required',
            'recipient_phone' => 'required',
            'recipient_postcode' => 'required',
            'recipient_state' => 'required',
            'recipient_country' => 'required',
            'courier_id' => 'required',
            'selectedBranch' => 'required',
            'amount_insured' => 'required_if:insurance,==,1|numeric|min:0',
            'outbound_products' => 'required',
            'invoice_slip' => 'nullable|mimes:jpeg,png,pdf',
            'payer_gst_vat' => 'required_unless:is_malaysia,true',
            'harm_comm_code' => 'required_unless:is_malaysia,true',
            'trade_term' => 'required_unless:is_malaysia,true',
            'payment_term' => 'required_unless:is_malaysia,true',
            'export_reason' => 'required_unless:is_malaysia,true'
        ]);

        if(empty(auth()->user()->address))
        {
            return response(json_encode(array('overall' => ['You must update your contact details in the My Profile page before proceeding'])), 422);
        }

        try {
            $outboundProducts = json_decode($request['outbound_products'], true);
 
            $json_validator = Validator::make(
                ['outbound_products' => $outboundProducts],
                ['outbound_products.*' => 'bail|product_exist|product_stock']
            );

            if($json_validator->fails()) {
                return response()->json($json_validator->messages(), 422);
            }

            $user = \Auth::user();

            $user->customers()->updateOrCreate(
                ['id' => $request->customer_id],
                [
                    'customer_name' => $request->recipient_name,
                    'customer_address' => $request->recipient_address,
                    'customer_address_2' => $request->recipient_address_2,
                    'customer_phone' => $request->recipient_phone,
                    'customer_postcode' => $request->recipient_postcode,
                    'customer_state' => $request->recipient_state,
                    'customer_country' => $request->recipient_country,
                ]);
            if(collect($outboundProducts)->sum('quantity') == 0)
            {
                if(request()->wantsJson()) {
                    return response(json_encode(array('outbound_products' => ['Please select at least 1 product'])), 422);
                }
            }
            
            //Check whether selected a branch
            if($request->selectedBranch == null) {
                if(request()->wantsJson()) {
                    return response(json_encode(array('selectedBranch' => ['Inbound must have a branch'])), 422);
                }
                return redirect()->back()->withErrors("Inbound must have a branch.");
            }
            //Check lot products
            // $lots = lot::where('branch_id', $request->selectedBranch)->get();
            // foreach($lots as $lot){
            //    foreach($outboundProducts as $outboundProduct) {
            //        dd($outboundProduct);
            //        $lot_product = $lot->products()->where('product_id' , $outboundProduct['id'])->get();
            //        $product = product::find($outboundProduct['id']);
            //        if($lot_product) {
            //             $lots_product = $lot->check_can_deduct_product($product, $outboundProduct['quantity']);
            //             if($lots_product == false) {
            //                 if(request()->wantsJson()) {
            //                     return response(json_encode(array('outbound_products' => ['Invalid outbounds quantity!'])), 422);
            //                 }
            //             }                
            //        }
            //    }
            // }

            $outbound = new Outbound($request->except(['business', 'is_malaysia']));
            $outbound->insurance = request()->has('insurance');
            
            $outbound->amount_insured = $outbound->insurance ? request()->amount_insured : 0;
            $outbound->user_id = $user->id;
            $outbound->branch_id = $request->selectedBranch;
            $outbound->is_business = $request->business == "true" ? true : false;
            $outbound->status = 'true';
            $outbound->process_status = 'pending';

            if( $request->hasFile('invoice_slip') )
            {
                $file = $request->file('invoice_slip');
                if($file->extension() !== 'pdf')
                {
                    $path = $file->hashName('public');
                    $image = Image::make($file);

                    $image->resize(705,null, function($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::put($path, (string) $image->encode());

                    // $product->picture = $file->store('public');
                    $outbound->invoice_slip = $path;
                }
                else
                {
                    $outbound->invoice_slip = $file->store('public');
                }
            } 

            foreach ($outboundProducts as $outboundProduct) {
                $product = $user->products()
                    ->where('id', $outboundProduct['id'])
                    ->firstOrFail();
                // Quantity used to mark down how many number of product
                // going to retrieve from user's lots
                $quantity = $outboundProduct['quantity']; //20

                foreach ($product->lots as $lot) {

                    // Check if the lot have enough products supply to the outbound request
                    $sumOfQuantityAndIncomingQuantity = $lot->pivot->quantity + $lot->pivot->incoming_quantity - $lot->pivot->outgoing_product; //10
                    if($sumOfQuantityAndIncomingQuantity >= $quantity) {
                        // We do not need to go to next lot anymore
                        $volumeAfterDeductProduct = $lot->left_volume + ($product->volume * $quantity);

                        $lot->update(['left_volume' => $volumeAfterDeductProduct]);

                        $newQuantityForOutgoingProduct = $lot->pivot->outgoing_product + $quantity;

                        $product->lots()->updateExistingPivot($lot->id, ['outgoing_product' => $newQuantityForOutgoingProduct]);

                        $outbound->products()->attach($product->id, ['quantity' => $quantity, 'lot_id' => $lot->id, 'remark' => $outboundProduct['remarks'], 'unit_value' => $outboundProduct['unit_value'], 'total_value' => $outboundProduct['total_value'], 'weight' => $outboundProduct['weight'], 'manufacture_country' => $outboundProduct['manufacture_country']]);

                        break;

                    } else if($sumOfQuantityAndIncomingQuantity > 0){
                        // If we still need to go to next lot to get product, means take everything out
                        $volumeAfterDeductProduct = $lot->left_volume + ($product->volume * $lot->pivot->quantity);

                        $lot->update(['left_volume' => $volumeAfterDeductProduct]);

                        $newQuantityForOutgoingProduct = $lot->pivot->outgoing_product + $sumOfQuantityAndIncomingQuantity;

                        $product->lots()->updateExistingPivot($lot->id, ['outgoing_product' => $newQuantityForOutgoingProduct]);

                        $outbound->products()->attach($product->id, ['quantity' => $lot->pivot->quantity, 'lot_id' => $lot->id, 'remark' => $outboundProduct['remarks'], 'unit_value' => $outboundProduct['unit_value'], 'total_value' => $outboundProduct['total_value'], 'weight' => $outboundProduct['weight'], 'manufacture_country' => $outboundProduct['manufacture_country']]);

                        // Update how many quantity left require to acquire from the other lot
                        $quantity -= $sumOfQuantityAndIncomingQuantity;
                    }
                }
            }
            $outbound->save();
            $outbound->notify(new OutboundStatusUpdateNotification($outbound));

        } catch (\Exception $exception) {

            return response()->json($exception->getMessage(), 422);

        }

        event(new EventTrigger('outbound'));

        return response()->json(['message' => 'Outbound order created successfully']);
    }

    public function show($outbound)
    {
        $outbound = Outbound::with(['tracking_numbers', 'courier'])->where('id', $outbound)->first();
        
        return $outbound;
    }

    /**
     * Display the products for the outbound.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function products(Outbound $outbound)
    {
        $products = $outbound->products()
                            ->select('products.name as name',
                                  'products.picture as picture',
                                  'outbound_product.quantity as quantity',
                                  'products.is_fragile as is_fragile',
                                  'products.is_dangerous as is_dangerous',
                                  'lots.name as lot_name')
                            ->join('lots', 'lots.id', '=', 'lot_id')
                            ->orderBy('lot_id')
                            ->get();

        return $products;
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
}
