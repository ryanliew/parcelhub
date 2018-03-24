<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\AccountVerification;
use App\Role;
use App\User;
use App\Http\Controllers\Controller;
use App\UserToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function register(Request $request) {

        $validator = $this->validator($request->all());

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        // Default all the registered user have user role
        $role = Role::where('name', 'user')->first();

        $user->attachRole($role);

        // Generate token used for email verification
        $token = new UserToken([
            'token' => str_random(60),
            'expire_at' => Carbon::now()->addMinute(5)
        ]);

        $user->tokens()->save($token);

        // Send email verification to users email
        $user->notify(new AccountVerification($user));

        return view('user.verify')->with([
            'message' => trans('auth.token_not_verify'),
            'id' => $user->id
        ]);
    }

    public function resend($id) {

        $user = User::find($id);

        // Generate new token and update old token
        $user->tokens()->update([
            'token' => str_random(60),
            'expire_at' => Carbon::now()->addMinute(5)
        ]);

        $user->notify(new AccountVerification($user));

        return redirect($this->redirectTo);
    }

    public function verify($token) {

        try {

            $userToken = UserToken::where('token', $token)->firstOrFail();

            $user = $userToken->user;

            if($user->verified) {

                return view('user.verify')->with([
                    'message' => trans('auth.token_verified'),
                ]);
            }

            if($userToken->expire_at->isFuture()) {

                $user->update(['verified' => true]);

            } else {

                return view('user.verify')->with([
                    'message' => trans('auth.token_expired'),
                    'id' => $userToken->user->id
                ]);
            }

        } catch (\Exception $ex) {

            return view('user.verify')->withErrors( [trans('auth.token_not_found')] );
        }

        return redirect($this->redirectTo);
    }
}
