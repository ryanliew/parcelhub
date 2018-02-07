<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
        $user = Socialite::driver($provider)->scopes(['profile', 'email', 'openid'])->user();

		$socialUser = null;
		
		$existUser = User::where('email', '=', $user->email)->first();
		
		if (!empty($existUser)) 
		{
			$socialUser = $existUser;
		}
		else
		{
			$socialUser = factory(\App\User::class)->create(['email' => $user->getEmail()]);	
		}

        auth()->login($socialUser, true);
		
        return redirect('/home');
		
        // return abort(500, 'User has no Role assigned, role is obligatory! You did not seed the database with the roles.');
    }
}
