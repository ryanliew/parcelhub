<?php

namespace App\Http\Controllers;

use App\Country;
use App\User;
use App\Accessibility;
use App\Branch;
use App\Notifications\Admin\AdminCreateUserNotification;
use App\Notifications\UserRegisteredNotification;
use App\Notifications\UserAccessBranchNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
        //please take note here
		if(request()->wantsJson()) {
            $user = auth()->user();
            $role = $user->roles()->first();

            $query = User::with('inbounds')->with('outbounds')->with('roles');
            
            if($role->name == 'superadmin') {
                return Controller::VueTableListResult($query);
            }
            $accessibility = $user->branches()->get();
            $branches_code = [];
            foreach($accessibility as $access) {
                $branch_code = $access->branch_code;
                array_push($branches_code, $branch_code);
            }
            // return Controller::VueTableListResult($query);
			return Controller::VueTableListResult($query->select('users.name',
                                                                'users.address',
                                                                'users.address_2',
                                                                'users.email',
                                                                'users.country', 
                                                                'users.created_at', 
                                                                'users.phone')
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
        // $branch = Branch::where('code', $branch_code)->first();
        // $accessibility = $branch->access;
        // $users = [];
        // foreach($accessibility as $access) {
        //     $roles = $access->users->roles()->first();
            
        //     if($roles->name == 'admin') {
        //         array_push($users, $access->users);
        //     }
        // }
        $users = User::all();

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
            'phone' => 'required',
            'postcode' => 'required',
            'state' => 'required',
            'country' => 'required',
            'id' => 'required',
            'password' => 'required_if:change_password,true|confirmed',
        ]);

    	$user = User::findOrFail(request()->id);

        $password = $request->change_password == 'true' ? bcrypt($request->password) : $user->password;

    	$user->update([
    		'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'country' => $request->country,
            'address_2' => $request->address_2,
            'password' => $password
    	]);

    	return ['message' => 'User profile updated.'];
    }

    public function approval(User $user)
    {
        $user = $user->toggleApproval();

        $message = $user->is_approved ? "approved" : "banned";
        
        return ['message' => "User has been " . $message];
    }

    public function store(Request $request)
    {
        $obj_branches = Branch::whereIn('id' , json_decode($request->branches))->get();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'phone' => 'required'
        ]);

        $user = User::where('email' , $request->email)->first();
        if(!$user) {
            $password = Str::random(8);
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($password);
            $user->address = $request->address;
            $user->address_2 = $request->address_2;
            $user->phone = $request->phone;
            $user->state = $request->state;
            $user->postcode = $request->postcode;
            $user->country = $request->country;
            $user->save();

            $user->notify(new AdminCreateUserNotification($user, $password));

        }
        else {
            
            $full_branch = $obj_branches->implode('branch_name', ',');
            
            $user->notify(new UserAccessBranchNotification($user, $full_branch));
        }
        $user->branches()->attach($obj_branches);
        return ['message' => "User $request->name has been created"];

    }
}
