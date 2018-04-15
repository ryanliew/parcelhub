<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    public function authenticated()
    {
        if(Auth::user()->verified) {
            if(Auth::user()->hasRole('admin')) {
                return redirect()->intended('dashboard');
            }
        } else {
            $id = Auth::user()->id;
            Auth::logout();

            return view('user.verify')->with([
                'message' => trans('auth.token_not_verify'),
                'id' => $id
            ]);
        }

        return redirect()->intended('lots');
    }
}
