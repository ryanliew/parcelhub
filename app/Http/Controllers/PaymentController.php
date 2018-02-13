<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Lot;
use App\User;
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

            // Retrieve all users purchased lot without being approve
            $payments = Payment::all();
//            $payments = Payment::whereStatus('false')->get()->filter(function ($payment) {
//                $purchased = $payment->user->lots->filter(function ($lot) {
//                    return $lot->status === 'false';
//                });
//                return $purchased->count() > 0;
//            });

            return view("payment.admin")->with('payments', $payments);

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
            'lots_purchase' => 'required',
            'payment_slip' => 'required|image',
        ]);

        $lots = $request->input('lots_purchase');

        $payment = new Payment();
        $payment->user_id = auth()->user()->id;
        $payment->picture = $request->file('payment_slip')->store('public');
        $payment->save();

        foreach($lots as $lot) {
             Lot::whereId($lot['id'])->update(['user_id' => auth()->id()]);
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
            Payment::where('id', '=', $value)->update(['status' => 'true']);
        }

        return redirect()->back();
    }
}
