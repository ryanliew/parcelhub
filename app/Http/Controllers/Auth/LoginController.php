<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        return $this->redirectParcelCenter($request, '/login');
    }

    // Handle By Parcelhub Center
    // public function authenticated()
    // {
    //     if(Auth::user()->verified && Auth::user()->is_approved) {
    //         if(Auth::user()->hasRole('admin')) {
    //             return redirect()->intended('dashboard');
    //         }
    //     } else {
    //         $id = Auth::user()->id;
    //         $message = Auth::user()->verified ? "You have been restricted from accessing the system. Please contact the administrator for help." : trans('auth.token_not_verify');
    //         $banned = Auth::user()->verified && !Auth::user()->is_approved ;
    //         Auth::logout();

    //         return view('user.verify')->with([
    //             'message' => $message,
    //             'id' => $id,
    //             'banned' => $banned
    //         ]);
    //     }

    //     return redirect()->intended('lots');
    // }
}
