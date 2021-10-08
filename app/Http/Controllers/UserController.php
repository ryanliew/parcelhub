<?php

namespace App\Http\Controllers;

use App\Country;
use App\User;
use App\Accessibility;
use App\Branch;
use App\Role;
use App\Notifications\Admin\AdminCreateUserNotification;
use App\Notifications\UserRegisteredNotification;
use App\Notifications\UserAccessBranchNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;

class UserController extends Controller
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
    
	public function page()
	{
		if(request()->wantsJson()) {
            $user = auth()->user();
            $role = $user->roles()->first();

            $query = User::with('inbounds')->with('outbounds');
            
            if($role->name == 'superadmin') {
                return Controller::VueTableListResult($query);
            }
            $accessibility = $user->access;
            $branches_code = [];
            foreach($accessibility as $access) {
                $branch_code = $access->branch_code;
                array_push($branches_code, $branch_code);
            }

			return Controller::VueTableListResult($query->select('users.name',
                                                                'users.address',
                                                                'users.address_2',
                                                                'users.email',
                                                                'users.country', 
                                                                'users.created_at', 
                                                                'users.phone',
                                                                'users.id'
                                                                )
                                                                ->where('verified', true)
                                                                ->leftJoin('lots', 'lots.user_id', '=', 'users.id')
                                                                ->whereIn('lots.branch_code', $branches_code)
                                                                ->distinct());
        }

		return view('user.page')
                ->with("countries", Country::with("states")->active()->get());
	}

    public function index($branch_code)
    {
        $branch = Branch::where('code', $branch_code)->first();
        $accessibility = $branch->access->pluck('user_id');

        $users = User::whereIn('id', $accessibility)->whereHas('roles', function ($query) {
            $query->where('role_id' , 1);
        })->get();

        return $users;
    }

    public function selector()
    {
        return User::where('verified', true)->withRole('user')->get();
    }

    public function lotSelector(User $user)
    {
        return $user->lots;
    }

    public function show()
    {	
    	return auth()->user();
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'id' => 'required',
            'password' => 'required_if:change_password,true|confirmed',
        ]);

        $user = User::findOrFail(request()->id);

        $password = $request->change_password == 'true' ? bcrypt($request->password) : $user->password;

        $access_token = session()->get('access_token');

        $url = env('PARCELHUB_CENTER_URL');
        $client = new Client();
    
        try{
			$response = $client->request('POST', $url.'/api/user/update', [
                "headers" => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer " . $access_token
                ],
                "form_params" => [
                    'email' => $user->email,
                    'name' => request()->name,
                    'password' => $request->change_password ? request()->password : null,
                ]
            ]);
		}
		catch (\Exception $e) {
			$response = $e->getResponse();
		}

        $responseContent = $response->getBody()->getContents();
		$responseJson = json_decode($responseContent);

        if(isset($responseJson->errors)) {
            $content =  [
                "message" => $responseJson->message,
                "errors" => $responseJson->errors
            ];
        }
        else if($responseJson->status == 1) {

            $user->update(
                ['name' => request()->name,
                    'address' => request()->address,
                    'postcode' => request()->postcode,
                    'city' => request()->city,
                    'state' => $request->state,
                    'country' => request()->country,
                    'password' => request()->has('password')? $password : $user->password,
                ]);
            
            return json_encode(['message' => $responseJson->message]);
        }

        return response($content, 422);
    }

    public function approval(User $user)
    {
        $user = $user->toggleApproval();

        $message = $user->is_approved ? "approved" : "banned";
        
        return ['message' => "User has been " . $message];
    }

    public function store(Request $request)
    {
        $obj_branches = Branch::whereIn('code' , json_decode($request->branches))->get();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'phone' => 'required|unique:users,phone|regex:/^(60)[0-46-9]*[0-9]{7,8}$/'
        ]);

        $user = User::where('email' , $request->email)->first();

        $access_token = session()->get('access_token');

        $url = env('PARCELHUB_CENTER_URL').'/api/user/create';

        $client = new Client();
        if(!$user) {
            try{
                $response = $client->request('POST', $url, [
                    "headers" => [
                        'Accept' => 'application/json',
                        'Authorization' => "Bearer " . $access_token
                    ],
                    "form_params" => [
                        'email' => request()->email,
                        'name' => request()->name,
                        'phone' => request()->phone,
                    ]
                ]);
            }
            catch (\Exception $e) {
                $response = $e->getResponse();
            }
    
            $responseContent = $response->getBody()->getContents();
            $responseJson = json_decode($responseContent);

            if(isset($responseJson->errors)) {
                $content =  [
                    "message" => $responseJson->message,
                    "errors" => $responseJson->errors
                ];
    
                return response($content, 422);
            }
            else if($responseJson->status == 1){
                $password = Str::random(8);
                $encrypted_password = Hash::make($password);
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = $encrypted_password;
                $user->address = $request->address;
                $user->address_2 = $request->address_2;
                $user->phone = $request->phone;
                $user->state = $request->state;
                $user->postcode = $request->postcode;
                $user->country = $request->country;
                $user->city = $request->city;
                $user->is_approved = true;
                $user->save();

                $role = Role::where('name', 'user')->first();

                $user->attachRole($role);

                $user->notify(new AdminCreateUserNotification($user));
    
            }
        }
        else {
            
            $full_branch = $obj_branches->implode('name', ',');
            
            $user->notify(new UserAccessBranchNotification($user, $full_branch));
        }

        $new_access = new Accessibility();
        $new_access->user_id = $user->id;
        $new_access->branch_code = $obj_branches[0]->code;
        $new_access->branch_id = 1;
        $new_access->save();

        if(isset($responseJson->message)) {
            return ['message' => $responseJson->message];
        }

        return ['message' => 'This account has been linked!'];

    }

}
