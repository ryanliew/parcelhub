<?php

namespace App\Http\Controllers;

use App\Courier;
use App\Lot;
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

            $products = Product::with('lots')->where('user_id', auth()->id())->get();

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
            'insurance' => 'required',
            'amount_insured' => 'sometimes|required',
            'products' => 'required',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required|min:1',
        ]);

        $inputs = $request->all();
        $outboundProducts = $inputs['products'];

        $user = \Auth::user();
        $outbound = new Outbound();

        try {
            $outbound->fill($inputs);

            if($inputs['insurance'] === 'yes') {
                $outbound->insurance = true;
                $outbound->amount_insured = $inputs['amount_insured'];
            } else {
                $outbound->insurance = false;
                $outbound->amount_insured = 0;
            }

            $outbound->user_id = $user->id;
            $outbound->status = 'true';
            $outbound->save();
        } catch (\Exception $e) {
            echo $e;
            abort(404);
        }

        foreach ($outboundProducts as $outboundProduct) {

            try {
                $product = $user->products()
                    ->where('id', $outboundProduct['id'])
                    ->firstOrFail();

                // Quantity used to mark down how many number of product
                // going to retrieve from user's lots
                $quantity = $outboundProduct['quantity'];

                if($quantity > $product->total_quantity) {
                    return redirect()->back()->withErrors("You don't have sufficient products in your LOT");
                }

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
            } catch (\Exception $e) {
                echo $e;
                abort(404);
            }
        }

        return redirect()->back()->withSuccess('Successfully create outbound order');
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
