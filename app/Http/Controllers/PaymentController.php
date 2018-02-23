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
        if(request()->wantsJson()) {
            return Controller::VueTableListResult(Payment::select("*"));
        }

        if(\Entrust::hasRole('admin')) {

            $payments = Payment::whereStatus('false')->get();

            return view("payment.admin")->with(compact('payments', 'lots'));

        } else {

            $lots = Lot::whereNull('user_id')
                ->where('status', '=', 'false')
                ->get();

            return view("payment.user")->with('lots', $lots);

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

        $this->validate($request, [
            'payment_slip' => 'required|image',
        ]);

        try {
            $lot_purchases = json_decode($request['lot_purchases'], true);

            $validator = \Validator::make(['lot_purchases' => $lot_purchases], [
                'lot_purchases.*.rental_duration' => 'required|integer|min:90',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = \Auth::user();

            $payment = new Payment();
            $payment->picture = $request->file('payment_slip')->store('public');
            $payment->user()->associate($user);
            $payment->save();

            foreach($lot_purchases as $lot_purchase) {

                $lot = Lot::find($lot_purchase['id']);

                $lot->rental_duration = $lot_purchase['rental_duration'];
                $lot->user()->associate($user);
                $lot->save();

                $lot->payments()->save($payment);
            }

        } catch (\Exception $exception) {

            return redirect()->back()->withErrors($exception->getMessage());

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
                $lot->update(['status' => 'true', 'expired_at' => Carbon::now()->addDays($lot->rental_duration)]);

            }
        }

        return redirect()->back()->withSuccess('Payment approved');
    }
}