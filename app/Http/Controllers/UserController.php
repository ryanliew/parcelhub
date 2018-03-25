<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
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
		if(request()->wantsJson())
			return Controller::VueTableListResult(User::with('inbounds')->with('outbounds'));

		return view('user.page');
	}

    public function index()
    {
    	return User::all();
    }

    public function selector()
    {
        return User::where('verified', true)->withRole('user')->get();
    }

    public function show()
    {	
    	return auth()->user();
    }

    public function update(Request $request)
    {
    	$this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'postcode' => 'required',
            'state' => 'required',
            'country' => 'required',
            'id' => 'required'
        ]);

    	$user = User::findOrFail(request()->id);

    	$user->update([
    		'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'country' => $request->country,
            'address_2' => $request->address_2
    	]);

    	return ['message' => 'User profile updated.'];
    }
}
