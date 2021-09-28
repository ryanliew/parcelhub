<?php

namespace App\Http\Controllers;

use App\Notifications\PaymentCreatedNotification;
use App\Payment;
use App\Lot;
use App\PaymentGatewayDefinition;
use App\Services\BillplzService;
use App\Settings;
use App\User;
use App\Accessibility;
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
            if(auth()->user()->hasRole('superadmin')) {
                return Controller::VueTableListResult(Payment::with('lots')
                                                                ->select('users.name as name',
                                                                        'picture as picture',
                                                                        'payments.status as status',
                                                                        'payments.created_at as created_at',
                                                                        'payments.price as price',
                                                                        'payments.id as id')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id'));
            }

            elseif(auth()->user()->hasRole('admin')){
                $branches = auth()->user()->access->pluck("branch_code");
                $lots = Lot::whereIn('branch_code', $branches)->get()->pluck('user_id');
                $users = User::whereIn('id', $lots)->get()->pluck('id');
                return Controller::VueTableListResult(Payment::select('users.name as name',
                                                'picture as picture',
                                                'payments.status as status',
                                                'payments.created_at as created_at',
                                                'payments.price as price',
                                                'payments.id as id')
                                                ->whereIn('user_id', $users)
                                                ->leftJoin('users', 'user_id', 'users.id'));
            }
            return Controller::VueTableListResult(auth()->user()->payments()->with('lots')
                                                                ->select('users.name as name',
                                                                        'picture as picture',
                                                                        'payments.status as status',
                                                                        'payments.created_at as created_at',
                                                                        'payments.price as price',
                                                                        'payments.id as id')
                                                                ->leftJoin('users', 'user_id', '=', 'users.id'));
        }

        if(\Entrust::hasRole('admin')) {

            $payments = Payment::whereStatus('pending')->get();

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
        return Controller::VueTableListResult(Payment::with('user')->with('lots')->where('status', 'pending'));
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

        $settings = Settings::all();
        $rental_duration = $settings->filter(function($value){return $value->setting_key == 'rental_duration';})->first()->setting_value;

        $this->validate($request, [
            'payment_definition_id' => 'required|exists:payment_gateway_definitions,id',
            'selectedBranch' => 'required',
            'rental_duration' => 'bail|required|integer|min:' . $rental_duration
        ], [
            "payment_definition_id:exists" => "Invalid payment method selected",
        ]);

        try {
            $lot_purchases = json_decode($request['lot_purchases'], true);

            $json_validator = \Validator::make(['lot_purchases' => $lot_purchases], ['lot_purchases' => 'required']);

            if ($json_validator->fails()) {

                return response()->json($json_validator->messages(), 422);
            }

            $user = \Auth::user();
            // Create a payment and associate lots here
            // Send user to payment gateway
            $definition = PaymentGatewayDefinition::find($request->payment_definition_id);
            $payment = Payment::create([
                "price" => $request->price,
                "user_id" => auth()->id(),
                "status" => Payment::STATUS_PENDING,
                "payment_gateway_definition_id" => $request->payment_definition_id,
                "payment_type" => $definition->category,
            ]);

            $billplz = new BillplzService($payment);
            $payment = $billplz->initialize();

            $payment->save();

            foreach($lot_purchases as $lot_purchase) {

                $lot = Lot::find($lot_purchase['id']);

                $lot->rental_duration = $lot_purchase['rental_duration'];
                $lot->branch_code = $request['selectedBranch'];
                $lot->user()->associate($user);
                $lot->save();

                $lot->payments()->save($payment);
            }

            $new_access = new Accessibility();
            $new_access->user_id = $user->id;
            $new_access->branch_code = $request['selectedBranch'];
            $new_access->branch_id = 1;
            $new_access->save();
            // $user->()->attach($request['selectedBranch']);

//            $user->notify(new PaymentCreatedNotification($payment->load('user', 'lots')));

        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 422);
        }
        
        return response()->json(['message' => 'Purchase order created', 'payment' => $payment]);
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

    public function approve(Request $request)
    {
        $payment = Payment::find($request->id);
        $payment->update(['status' => 'approved']);

        $lotIds = $payment->lots()->pluck('lot_id')->toArray();

        foreach($lotIds as $_key => $_value) {

            $lot = Lot::find($_value);
            $lot->update(['status' => 'approved', 'expired_at' => Carbon::now()->addMonths($lot->rental_duration)]);
        }

        return response()->json(['message' => 'Payment approved']);
    }

    public function billplzresponse()
    {
        info(request()->all());
    }
}