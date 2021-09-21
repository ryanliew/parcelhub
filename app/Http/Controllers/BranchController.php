<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Role;
use App\Lot;
use App\Accessibility;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $rules = [
        'name' => 'required'
    ];

     public function index()
    {
        return Controller::VueTableListResult(Branch::query());
    }

    public function page()
    {
        return view('branch.page');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, $this->rules);

        $branch = new Branch;
        $branch->code = $request->code;
        $branch->name = $request->name;
        $branch->contact = $request->phone;
        $branch->address = $request->address;
        $branch->state = $request->state;
        $branch->postcode = $request->postcode;
        $branch->country_code = $request->country;
        $branch->is_warehouse_allowed = true;

        $branch->save();

        if(request()->wantsJson())
        {   
            return ["message" => $branch->branch_name . " created successfully."];
        }
        return redirect()->back()->withSuccess($branch->branch_name . " created successfully.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $all_users = Role::where('name', 'admin')->with('users')->get()->pluck('users')->flatten();

        $branches = Branch::where('code', $code)->get();
        $branch_users = [];
        foreach($branches as $branch) {
            $accessibility = $branch->access;
            foreach($accessibility as $access) {
                array_push($branch_users, $access->users);
            }
        }
        $branch_users = collect($branch_users);
        $all_users_id = $all_users->pluck('id');
        $branch_users_id = $branch_users->pluck('id');

        $not_in_branch_users_id = $all_users_id->diff($branch_users_id);
        $not_in_branch_users = $all_users->whereIn('id', $not_in_branch_users_id);

        return [$branch_users, $not_in_branch_users->values()];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $branch = Branch::where('code',$request->branch_code)->first();
        $user_id = json_decode($request->id);
        //replace sync 
        $branch->access()->delete();
        if(!empty($user_id)) {
            foreach($user_id as $user) {
                Accessibility::create([
                    'user_id' => $user,
                    'branch_code' => $request->branch_code,
                    'branch_id' => $branch->id
                ]);
            }
        }

        if(request()->wantsJson())
        {   
            return ["message" => "Accessibilities for " . $branch->branch_name . '\'s branch updated successfully.'];
        }

        return redirect()->back()->withSuccess($branch->branch_name . ' updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, $this->rules);
        $branch = Branch::where('code', $request->code)->first();
        $branch->code = $request->code;
        $branch->name = $request->name;
        $branch->contact = $request->phone;
        $branch->address = $request->address;
        $branch->state = $request->state;
        $branch->postcode = $request->postcode;
        $branch->country_code = $request->country;

        $branch->save();

        if(request()->wantsJson())
        {   
            return ["message" => "Branch " . $branch->branch_name . ' updated successfully.'];
        }

        return redirect()->back()->withSuccess($branch->branch_name . ' updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy($branch_code)
    {
        $branch = Branch::where('code', $branch_code)->first();
        $branch->delete();
        Accessibility::where('branch_code' , $branch_code)->delete();
       
        if(request()->wantsJson())
        {   
            return ["message" => "Branch " . $branch->branch_name . ' deleted successfully.'];
        }

        return redirect()->back()->withSuccess($branch->branch_name . ' deleted successfully.');
    }
    public function selector() {
        $user = auth()->user();

        if($user->hasRole('superadmin')) {
            $branch = Branch::all();
        }
        elseif($user->hasRole('admin')) {
            $accessibility = $user->branches;
            $branch = [];
            foreach($accessibility as $access) {
                $access_branch = $access->branches;
                array_push($branch , $access_branch);
            }
        }
        else {
            $lots_branch_id = $user->lots->pluck('branch_code')->unique();

            $branch = Branch::whereIn('code', $lots_branch_id)->get();
        }

       return $branch;
    }

    public function all_selector() {
        return Branch::all();
    }

    public function add_default_branch_code($tables = []) {
        foreach($tables as $table) {
            $rawdata = $table::all();
        
            foreach($rawdata as $data) {
                if($data->branch_code == '') {
                    $data->branch_code = 'WHQ';
                    $data->save();
                }
            }
        }
        return 'Successfully update table';
    }
}
