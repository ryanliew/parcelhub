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

        // Generate token used for email verification
        $token = new UserToken([
            'token' => str_random(60),
            'expire_at' => Carbon::now()->addMinute(5)
        ]);

        $authUser->tokens()->save($token);

        User::admin()->first()->notify(new UserRegisteredNotification($authUser));
        // Send email verification to users email
        $authUser->notify(new AccountVerificationNotification($authUser));
		
        return view('user.verify')->with([
            'message' => trans('auth.token_not_verify'),
            'id' => $authUser->id,
            'banned' => false
        ]);
    }

    public function findOrCreateUser($user)
    {
        if($authUser = User::where('email', '=', $user->email)->first()) {
            return $authUser;
        }

        $user = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'verified' => true
        ]);

        $role = Role::where('name', '=', 'user')->first();

        $user->attachRole($role);

        return $user;
    }
}
