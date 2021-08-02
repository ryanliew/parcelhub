<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
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
    	if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
    		return view('dashboard');
    }
}
