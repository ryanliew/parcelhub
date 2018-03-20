<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Outbound;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;

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
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->wantsJson()) {
            $user = auth()->user();
            if($user->hasRole('admin'))
                return Controller::VueTableListResult( Outbound::select('outbounds.id as id',
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
                                                                    'users.name as customer'
                                                                    )
                                                                ->leftJoin('couriers', 'courier_id', '=', 'couriers.id')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
                                                                ->orderBy('outbounds.created_at', 'desc') );
            else
                return Controller::VueTableListResult( $user->outbounds()
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
                                                                    'outbounds.recipient_postcode'
                                                                    )
                                                            ->leftJoin('couriers', 'courier_id', '=', 'couriers.id')
                                                            ->orderBy('outbounds.created_at', 'desc') );

        }

        if(\Entrust::hasRole('admin')) {

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
       return Controller::VueTableListResult( Outbound::select('outbounds.id as id',
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
                                                                    'users.name as customer'
                                                                    )
                                                                ->leftJoin('couriers', 'courier_id', '=', 'couriers.id')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id')
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

    public function report($id)
    {
        $outbound = Outbound::find($id);

        $pdf = PDF::loadView('outbound.report', compact('outbound'));

        return $pdf->setPaper('A4')->download('outbound-report.pdf');
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
            'amount_insured' => 'required_if:insurance,==,1|numeric|min:0',
            'outbound_products' => 'required',
        ]);

        if(empty(auth()->user()->address))
        {
            return response(json_encode(array('overall' => ['You must update your contact details in the My Profile page before proceeding'])), 422);
        }

        try {
            $outboundProducts = json_decode($request['outbound_products'], true);

            $json_validator = Validator::make(['outbound_products' => $outboundProducts], [
                'outbound_products.*' => 'bail|product_exist|product_stock',
            ]);

            if($json_validator->fails()) {
                return response()->json($json_validator->messages(), 422);
            }

            $user = \Auth::user();

            $outbound = new Outbound($request->all());
            $outbound->insurance = request()->has('insurance');
            $outbound->amount_insured = $outbound->insurance ? request()->amount_insured : 0;
            $outbound->user_id = $user->id;
            $outbound->status = 'true';
            $outbound->process_status = 'pending';
            $outbound->save();

            foreach ($outboundProducts as $outboundProduct) {
                $product = $user->products()
                    ->where('id', $outboundProduct['id'])
                    ->firstOrFail();

                // Quantity used to mark down how many number of product
                // going to retrieve from user's lots
                $quantity = $outboundProduct['quantity'];

                foreach ($product->lots as $lot) {

                    // Check if the lot have enough products supply to the outbound request
                    if($lot->pivot->quantity >= $quantity) {

                        $volumeAfterDeductProduct = $lot->left_volume + ($product->volume * $quantity);

                        $lot->update(['left_volume' => $volumeAfterDeductProduct]);

                        // Update remaining product left in the lot
                        $numOfProductLeft = $lot->pivot->quantity - $quantity;

                        // Lot with 0 quantity will be detach else update the remaining available quantity
                        if($numOfProductLeft === 0) {

                            $product->lots()->detach($lot->id);

                        } else {

                            $product->lots()->updateExistingPivot($lot->id, ['quantity' => $numOfProductLeft]);;
                        }

                        $outbound->products()->attach($product->id, ['quantity' => $quantity, 'lot_id' => $lot->id]);

                        break;

                    } else {

                        $volumeAfterDeductProduct = $lot->left_volume + ($product->volume * $lot->pivot->quantity);

                        $lot->update(['left_volume' => $volumeAfterDeductProduct]);

                        $product->lots()->detach($lot->id);

                        $outbound->products()->attach($product->id, ['quantity' => $lot->pivot->quantity, 'lot_id' => $lot->id]);

                        // Update how many quantity left require to acquire from the other lot
                        $quantity -= $lot->pivot->quantity;
                    }
                }
            }

        } catch (\Exception $exception) {

            return response()->json($exception->getMessage(), 422);

        }

        return response()->json(['message' => 'Outbound order created successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Outbound $outbound)
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
}
