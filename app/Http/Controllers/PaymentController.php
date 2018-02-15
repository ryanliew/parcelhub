<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Lot;
use App\Settings;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Entrust::hasRole('admin')) {

            $payments = Payment::whereStatus('false')->get();

            return view("payment.admin")->with(compact('payments', 'lots'));

        } else {

            $lots = Lot::whereNull('user_id')
                ->where('status', '=', 'false')
                ->get();

            $rental_duration = (int)Settings::where('key', '=', 'rental_duration')->value('value');

            return view("payment.user")->with(compact('lots', 'rental_duration'));

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

    public function purchase(Request $request) {

        $rental_duration = (int)Settings::where('key', '=', 'rental_duration')->value('value');

        $this->validate($request, [
            'lots_purchase' => 'required',
            'lots_purchase.*.rental_duration' => 'required|numeric|min:'.$rental_duration,
            'payment_slip' => 'required|image',
        ]);

        dd(Input::file('cat.jpg'));
        $lots = $request->input('lots_purchase');

        $payment = new Payment();
        $payment->user_id = auth()->user()->id;
        $payment->picture = $request->file('payment_slip')->store('public');
        $payment->save();

        foreach($lots as $lot) {
            if(isset($lot['id'])) {
                $purchasedLot = Lot::find($lot['id']);
                $purchasedLot->update(['user_id' => auth()->id(), 'rental_duration' => $lot['rental_duration']]);
                $purchasedLot->payments()->attach([$payment->id]);
            }
         }

         return redirect()->back()->withSuccess('Successfully purchase');
     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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

    public function approve(Request $request)
    {
        $this->validate($request, [
            'payments' => 'required'
        ]);

        foreach ($request->input('payments') as $key => $value) {

            $payment = Payment::find($value);
            $payment->update(['status' => 'true']);

            $lotIds = $payment->lots()->pluck('lot_id')->toArray();

            foreach($lotIds as $_key => $_value) {
                $lot = Lot::find($_value);

                $carbon = new Carbon();
                $carbon->addDays($lot->rental_duration);

                $lot->update(['status' => 'true', 'expired_at' => $carbon->toDateTimeString()]);
            }
        }

        return redirect()->back()->withSuccess('Payment approved');
    }
}