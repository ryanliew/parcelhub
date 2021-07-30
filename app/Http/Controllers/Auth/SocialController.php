<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\AccountVerificationNotification;
use App\Notifications\UserRegisteredNotification;
use App\Role;
use App\User;
use App\UserToken;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Socialite;

class SocialController extends Controller
{
    /**
     * Redirect the user to the social provider authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {		
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from social provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $authUser = $this->findOrCreateUser($socialUser);

        // User is new or not verified
        if($authUser['is_create'] || !$authUser['user']->verified) {
            
            // We need to see if the user already has an active token
            if($authUser['user']->tokens()->count() == 0 || $authUser['user']->tokens()->first()->is_expired) {
                // Delete the token and generate a new token for email verification
                $authUser['user']->tokens()->delete();

                // Generate token used for email verification
                $token = new UserToken([
                    'token' => str_random(60),
                    'expire_at' => Carbon::now()->addMinute(5)
                ]);

                $authUser['user']->tokens()->save($token);

                User::superadmin()->first()->notify(new UserRegisteredNotification($authUser['user']));

                // Send email verification to users email
                $authUser['user']->notify(new AccountVerificationNotification($authUser['user']));
            }             
    		
            return view('user.verify')->with([
                'message' => trans('auth.token_not_verify'),
                'id' => $authUser['user']->id,
                'banned' => false
            ]);
        }

        // Not new user
        auth()->login($authUser['user']);

        // Check if user is verified and approved by admin
        if($authUser['user']->verified && $authUser['user']->is_approved) {
            if($authUser['user']->hasRole('admin')) {
                return redirect()->intended('dashboard');
            } else {
                return redirect()->intended('lots');
            }
        } else {
            // User is not verified / approved by admin
            $id = $authUser['user']->id;
            $message = $authUser['user']->verified ? "You have been restricted from accessing the system. Please contact the administrator for help." : trans('auth.token_not_verify');
            $banned = $authUser['user']->verified && !$authUser['user']->is_approved ;
            auth()->logout();

            return view('user.verify')->with([
                'message' => $message,
                'id' => $id,
                'banned' => $banned
            ]);
        }
    }

    public function findOrCreateUser($user)
    {
        if($authUser = User::where('email', '=', $user->email)->first()) {
            return ['user' => $authUser, 'is_create' => false];
        }

        $user = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'verified' => true
        ]);

        $role = Role::where('name', '=', 'user')->first();

        $user->attachRole($role);

        return ['user' => $user, 'is_create' => true];
    }
}
