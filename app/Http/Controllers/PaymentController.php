<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;

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
            $payments = Payment::all()->filter(function ($payment) {
                return $payment->status === 'false';
            });

            $cat = Payment::all()->filter(function ($payment) {
                return $payment->status === 'false';
            });

            return view("payment.admin")->with('payments', $payments);
        }

        return view("payment.user");
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
            'paymentSlip' => 'required',
        ]);

        //Store image file
        $path = $request->file('paymentSlip')->store('public');

        $payment = new Payment();
        $payment->user_id = auth()->user()->id;
        $payment->picture = $path;
        $payment->save();

        return redirect()->back()->withSuccess('Upload successfully');
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
            Payment::where('id', '=', $value)->update(['status' => 'true']);
        }

        return redirect()->back();
    }
}
