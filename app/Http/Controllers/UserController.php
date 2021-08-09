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

    public function index()
    {
    	return User::all();
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
        $error_message = [];
        $error_count = 0;
       
        if($request->name == null)
        {
            $error_message['name'] = ['Please fill in the name input!'];
            $error_count++;
        }
        
        if($request->email == null)
        {
            $error_message['email'] = ['Please fill in the email input!'];
            $error_count++;
        }

        if($request->address == null)
        {
            $error_message['address'] = ['Please fill in the address input!'];
            $error_count++;
        }

        if($request->phone == null)
        {
            $error_message['phone'] = ['Please fill in the phone input!'];
            $error_count++;
        }
        if($error_count != 0) {
            if(request()->wantsJson()) {
                return response(json_encode($error_message), 422);
            } 
        }

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

            foreach($branches as $branch) {
                $access = new Accessibility;
                $access->user_id = $user['id'];
                $access->branch_id = $branch;
    
                $access->save();
            }

            $user->password = $password;
            $user->notify(new AdminCreateUserNotification($user));

            return ['message' => "User $request->name has been created"];
        }
        else {
            foreach($branches as $branch) {
                $access = new Accessibility;
                $access->user_id = $check_email[0]->id;
                $access->branch_id = $branch;
                
                // $access->save();
            }
            if(count($branches) > 1){
                $array_branch = [];
                $full_branch = Branch::select('branch_name')->whereIn('id', $branches)->get();
                foreach($full_branch as $branch) {
                    array_push($array_branch, $branch->branch_name);
                }
                $full_branch = implode(",", $array_branch);
            }else {
                $full_branch = Branch::select('branch_name')->where('id', $branches)->get();
                $full_branch = $full_branch[0]->branch_name;
            }
            
            $check_email[0]->notify(new UserAccessBranchNotification($check_email[0], $full_branch));
            return ['message' => "User $request->name exist and have been added to the selected branches."];
        }

    }
}
