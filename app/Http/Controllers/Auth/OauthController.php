<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Role;
use App\User;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Support\Facades\Auth;

class OauthController extends Controller
{
    public function oauthCallback(Request $request)
    {
        $client = new Client();
        
        // Get Access Token Based on the Authorization Code
        $url = env('PARCELHUB_CENTER_URL') . '/oauth/token';
        $callback_url = env('APP_URL') . '/oauth/callback';
        // $callback_url = 'http://127.0.0.1:8080/oauth/callback';
        $response = $client->post($url, [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('PARCELHUB_CLIENT_ID'),
                'client_secret' => env('PARCELHUB_CLIENT_SECRET'),
                'redirect_uri' => $callback_url,
                'code' => $request->code,
            ],
        ]);

        $response_json = json_decode($response->getBody()->getContents());
        if($response_json->access_token) 
        {
            session([
                "access_token" => $response_json->access_token, 
                "refresh_token" => $response_json->refresh_token,
            ]);
        }

        return redirect()->route('oauth.user');
    }

    public function getAuthUser(Request $request) 
    {
        $access_token = session()->get('access_token');

        $client = new Client([
    		'base_uri' => env('PARCELHUB_CENTER_URL'),
    		"headers" => [
    			'Accept' => 'application/json',
                'Authorization' => "Bearer " . $access_token
    		]
		]);
		
		try{
			$response = $client->request('GET', '/api/user');
		}
		catch (\Exception $e) {
			$response = $e->getResponse();
		}

		$responseContent = $response->getBody()->getContents();
		$responseJson = json_decode($responseContent);

        if($responseJson && property_exists($responseJson, 'email')) 
        {
            $user = User::where('email', $responseJson->email)->first();
            // New User in current system, auto register
            if(!$user) {
                //Handle non-existing user in POS system
                $user = User::create([
                    'name' => $responseJson->name,
                    'email' => $responseJson->email,
                    'password' => bcrypt($responseJson->email),
                    'verified' => true,
                    'is_approved' => true,
                ]);
                
                Auth::login($user, true);  
                // Default all the registered user have user role
                $role = Role::where('name', 'user')->first();

                $user->attachRole($role);
                User::superadmin()->first()->notify(new UserRegisteredNotification($user));
            } else {
                $user->update([
                    'name' => $responseJson->name,
                    'phone' => $responseJson->phone,
                ]);
                Auth::login($user, true);
            }

            if($user->is_approved) {
                if($user->hasRole('admin')) {
                    return redirect()->intended('dashboard');
                }
            } else {
                return $this->logout();
            }

            return redirect()->intended('lots'); 
        }

        return redirect('/');
    }

    public function resetPassword(Request $request)
    {
        return $this->redirectParcelCenter($request, '/user/setpassword?email='.$request->email);
    }

    public function logout() 
    {
        Auth::logout();

        $client_id = env('PARCELHUB_CLIENT_ID');
        $url = '/';
        if($client_id) 
        {
            $centerUrl = env('PARCELHUB_CENTER_URL') . '/oauth/logout?';
            $callback_url = env('APP_URL') . '/';
            $query = http_build_query([
                'client_id' => $client_id,
                'redirect_uri' =>  $callback_url,
            ]);

            $url = $centerUrl . $query;
        }
        return redirect($url);
    }
}
