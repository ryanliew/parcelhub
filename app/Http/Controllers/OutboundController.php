<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Outbound;
use App\Product;
use App\User;
use Illuminate\Http\Request;

class OutboundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Entrust::hasRole('admin')) {

            return view('outbound.admin');
        } else {

            $products = Product::where('user_id', auth()->id())->get();

            $couriers = Courier::all();

            return view('outbound.user')->with(compact('products', 'couriers'));
        }
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
            'recipient_name' => 'required',
            'recipient_address' => 'required',
            'courier_id' => 'required',
            'products' => 'required',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required',
            'products.*.dangerous' => 'required',
            'products.*.insurance' => 'required',
            'products.*.amount_insured' => 'sometimes|required',
            'products.*' => 'lot_space',
        ]);

        $inputs = $request->all();

        $outboundProducts = $inputs['products'];

        $outbound = new Outbound();
        $outbound->fill($inputs);

        // Auto assign all the product into user available lot
        // how many product can enter into lot is calculate based on
        // the total lot left volume and total volume of the products
        foreach ($outboundProducts as $key => $value) {

            $product = Product::find($outboundProducts[$key]['id']);

            $user = User::with(['lots'])->find(auth()->id());

            $lots = $user->lots()->where('left_volume', '>', 0)->get();

            foreach ($lots as $lot) {

                if($outboundProducts[$key]['quantity'] == 0) {
                    break;
                }

                echo "Lot : $lot->id, have : $lot->left_volume space left\n";

                $totalProductVolume = $product->volume * $outboundProducts[$key]['quantity'];

                if($lot->left_volume >= $totalProductVolume) {

                    $lot->products()->attach($product->id, ['quantity' => $outboundProducts[$key]['quantity']]);

                    echo "Lot : $lot->id, stored : Product $product->id x ". $outboundProducts[$key]['quantity'] . "\n";

                    $lot->update(['left_volume' => $lot->left_volume - $totalProductVolume]);

                    // If current lot space able to store all the products
                    // then the current product quantity should set to 0
                    // prevent it looking for next available lot
                    $outboundProducts[$key]['quantity'] = 0;
                }

                // Breakdown large amount of product's quantity to suitable value
                // for the current lot left volume
                else {

                    $numberOfQuantityAbleToFitIn = intdiv($lot->left_volume, $product->volume);

                    echo "Lot : $lot->id : can store $numberOfQuantityAbleToFitIn products\n";

                    $lot->products()->attach($product->id, ['quantity' => $numberOfQuantityAbleToFitIn]);

                    echo "Lot : $lot->id, stored : Product $product->id x $numberOfQuantityAbleToFitIn quantity\n";

                    // Deduct lot's volume based on total number of product * quantity have been stored
                    $lotSpace = intval($lot->left_volume) - ($product->volume * $numberOfQuantityAbleToFitIn);

                    $lot->update(['left_volume' => $lotSpace]);

                    // Update remaining product's quantity it will be store to next lot with available space
                    $outboundProducts[$key]['quantity'] = $outboundProducts[$key]['quantity'] - $numberOfQuantityAbleToFitIn;
                }

            }

        }

        foreach($inputs['products'] as $product) {
            $outbound->user_id = $user->id;
            $outbound->product = $product['id'];
            $outbound->dangerous = $product['dangerous'] === 'yes' ? true : false;

            if($product['insurance'] === 'yes') {
                $outbound->insurance = true;
                $outbound->amount_insured = $product['amount_insured'];
            } else {
                $outbound->insurance = false;
                $outbound->amount_insured = 0;
            }

            $outbound->status = 'true';
            $outbound->save();
            $outbound->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        return redirect()->back();
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
}
