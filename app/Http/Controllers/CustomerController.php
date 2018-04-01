<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $rules = [
        'name' => 'required',
        'address' => 'required',
        'address2'  => 'required',
        'phone' => 'required',
        'postcode' => 'required',
        'state' => 'required',
        'country' => 'required'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Return the view which contains the vue page for customers
     * @return \Illuminate\Http\Response
     */
    public function page()
    {
        return view('customer.page');
    }

    public function index()
    {
        if(request()->wantsJson())
        {
            $user = auth()->user();
            if($user->hasRole('admin'))
                return Controller::VueTableListResult(Customer::with(["users", "customers"])->select('customers.name as name',
                                        'customers.address as address',
                                        'customers.address2 as address2',
                                        'customers.postcode as postcode',
                                        'customers.state as state',
                                        'customers.country as country',
                                        'customers.phone as phone',
                                        'users.name as user_name',
                                        'users.id as user_id'
                                    )
                                ->leftJoin('users', 'customers.user_id', '=', 'users.id')
                                ->where('status', true));
            else
                return Controller::VueTableListResult(auth()->user()->customers()->with(["users", "customers"])
                                                                                ->select('customers.name as name',
                                        'customers.address as address',
                                        'customers.address2 as address2',
                                        'customers.postcode as postcode',
                                        'customers.state as state',
                                        'customers.country as country',
                                        'customers.phone as phone',
                                        'users.name as user_name',
                                        'users.id as user_id'
                                    )
                                ->leftJoin('users', 'customers.user_id', '=', 'users.id')
                                                                                ->where('status', true)
                                                                            );
        }

        $auth = \Auth::user();
        $customers = Customer::where('user_id', $auth->id)->get();

        return view('customer.index', compact('customers'));
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
        //
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
    public function update(Request $request)
    {
        $this->validate($request, $this->rules);
        
        $customer = Customer::find($request->customer);
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->address2 = $request->address_2;
        $customer->phone = $request->phone;
        $customer->postcode = $request->postcode;
        $customer->state = $request->state;
        $customer->country = $request->country;
        $customer->save();
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
