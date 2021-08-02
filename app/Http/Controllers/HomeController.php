<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        if(auth()->user()->hasRole('admin'))
        {
            return redirect(route('dashboard'));
        }
        else if(auth()->user()->hasRole('superadmin')){
            return redirect(route('branches'));
        }
        else {
            return redirect(route('lots'));
        }
    }
}
