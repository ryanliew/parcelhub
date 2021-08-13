<?php

namespace App\Http\Controllers;

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
		if(request()->wantsJson())
			return Controller::VueTableListResult(User::with('inbounds')->with('outbounds')->with('roles'));

		return view('user.page');
	}

    public function index(Branch $branch)
    {
        $user = $branch->users()->join('role_user' , 'role_user.user_id' , '=' , 'users.id')
                                ->where('role_user.role_id', '2')
                                ->get();
        return $user;
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
        $branches = json_decode($request->branches);
        $obj_branches = Branch::whereIn('id' , $branches)->get();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone' => 'required'
        ]);

        $check_email = User::where('email' , $request->email)->get();
        if($check_email->isEmpty()) {
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

            foreach($obj_branches as $branch) {
                $branch->users()->attach($user['id']);
            }

            $user->password = $password;
            $user->notify(new AdminCreateUserNotification($user));

        }
        else {
            
            foreach($obj_branches as $branch) {
                $branch->users()->attach($check_email[0]->id);
            }
            if(count($branches) > 1){
                $array_branch = [];
                $full_branch = Branch::select('branch_name')->whereIn('id', $branches)->get();
                
                $full_branch = $full_branch->implode('branch_name', ',');
            }else {
                $full_branch = Branch::select('branch_name')->where('id', $branches)->get();
                $full_branch = $full_branch[0]->branch_name;
            }
            
            $check_email[0]->notify(new UserAccessBranchNotification($check_email[0], $full_branch));
        }

        return ['message' => "User $request->name has been created"];

    }
}
