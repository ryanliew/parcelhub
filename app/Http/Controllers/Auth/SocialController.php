<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        auth()->login($authUser);
		
        return redirect()->action('HomeController@index');
    }

    public function findOrCreateUser($user)
    {
        if($authUser = User::where('email', '=', $user->email)->first()) {
            return $authUser;
        }

        return User::create([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
