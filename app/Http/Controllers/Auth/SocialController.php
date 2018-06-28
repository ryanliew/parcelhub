<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Database\Eloquent\Model;

use App\User;
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

        $user->tokens()->save($token);

        User::admin()->first()->notify(new UserRegisteredNotification($user));
        // Send email verification to users email
        $user->notify(new AccountVerificationNotification($user));
		
        return view('user.verify')->with([
            'message' => trans('auth.token_not_verify'),
            'id' => $user->id,
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
