<?php

namespace App\Http\Controllers;

use App\Notifications\AccountVerificationNotification;
use App\Role;
use App\User;
use App\UserToken;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubuserController extends Controller
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
        return view('subusers.page');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = auth()->user()->subusers();

        if(auth()->user()->hasRole('admin')) 
        {
            $query = User::withRole('subuser')->get();
        }

        return Controller::VueTableListResult($query);
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
            'name' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:users',
            'user_id' => 'required',
            'phone' => 'required',
            'postcode' => 'required',
            'state' => 'required',
            'country' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = User::create([
                    'is_subuser' => true,
                    'name' => $request->name,
                    'email' => $request->email,
                    'parent_id' => $request->user_id,
                    'phone' => $request->phone,
                    'state' => $request->state,
                    'country' => $request->country,
                    'postcode' => $request->postcode,
                    'address' => $request->address,
                    'address_2' => $request->address_2,
                    'password' => bcrypt($request->password)
                ]);

        // Default subusers to have subuser role
        $role = Role::where('name', 'subuser')->first();

        $user->attachRole($role);

        // Generate token used for email verification
        $token = new UserToken([
            'token' => str_random(60),
            'expire_at' => Carbon::now()->addMinute(5)
        ]);

        $user->tokens()->save($token);

        $user->notify(new AccountVerificationNotification($user));

        if($request->wantsJson())
        {
            return ['message' => 'Subuser created successfully'];
        }
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
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'postcode' => 'required',
            'state' => 'required',
            'country' => 'required',
            'id' => 'required',
            'password' => 'required_if:change_password,true|confirmed',
        ]);

        $password = $request->change_password == 'true' ? bcrypt($request->password) : $user->password;

        $user->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'country' => $request->country,
            'address_2' => $request->address_2,
            'password' => $password
        ]);

        if($request->wantsJson())
        {
            return ['message' => 'Subuser profile updated successfully'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        if(request()->wantsJson())
        {
            return ['message' => 'Subuser access revoked successfully'];
        }
    }
}
