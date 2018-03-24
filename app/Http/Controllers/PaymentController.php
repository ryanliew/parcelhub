<?php

namespace App\Http\Controllers;

use App\Notifications\PaymentCreatedNotification;
use App\Payment;
use App\Lot;
use Settings;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PaymentController extends Controller
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
    
    public function page()
    {
        

        return view("payment.page");

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->wantsJson()) {
            if(auth()->user()->hasRole('admin'))
                return Controller::VueTableListResult(Payment::with('user')->with('lots'));
            return Controller::VueTableListResult(auth()->user()->payments()->with('user')->with('lots'));
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

    public function indexPending()
    {
        return Controller::VueTableListResult(Payment::with('user')->with('lots')->where('status', 'false'));
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

//        $rental = 'required|min:'.(int)Settings::get('rental_duration');
        $this->validate($request, [
            'payment_slip' => 'required|image',
            'rental_duration' => 'required|integer|min:' . Settings::get('rental_duration')
        ], ['rental_duration.min' => 'The minimum rental duration is ' . Settings::get('rental_duration') . ' months']);

        try {
            $lot_purchases = json_decode($request['lot_purchases'], true);

            $json_validator = \Validator::make(['lot_purchases' => $lot_purchases], [
                'lot_purchases.*.rental_duration' => 'required|integer|min:1',
            ], ['lot_purchases.$key.rental_duration' => 'Rental duration is required']);

            if ($json_validator->fails()) {
                return response()->json($json_validator->messages(), 422);
            }

            $user = \Auth::user();

            $payment = new Payment();
            $payment->picture = $request->file('payment_slip')->store('public');
            $payment->price = $request->price;
            $payment->user()->associate($user);
            $payment->save();

            foreach($lot_purchases as $lot_purchase) {

                $lot = Lot::find($lot_purchase['id']);

                $lot->rental_duration = $lot_purchase['rental_duration'];
                $lot->user()->associate($user);
                $lot->save();

                $lot->payments()->save($payment);
            }

            $user->notify(new PaymentCreatedNotification($payment->load('user', 'lots')));

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 422);
        }

        return response()->json(['message' => 'Purchase order created']);
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
    public function show(Lot $lot)
    {
        return $lot->payments()->with('user')->with('lots')->latest()->where('user_id', $lot->user_id)->first();
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
        $payment = Payment::find($request->id);
        $payment->update(['status' => 'true']);

        $lotIds = $payment->lots()->pluck('lot_id')->toArray();

        foreach($lotIds as $_key => $_value) {

            $lot = Lot::find($_value);
            $lot->update(['status' => 'true', 'expired_at' => Carbon::now()->addMonths($lot->rental_duration)]);

        }

        return response()->json(['message' => 'Payment approved']);
    }
}