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

        $branch = new branch;
        $branch->codename = $request->codename;
        $branch->branch_name = $request->name;
        $branch->branch_phone = $request->phone;
        $branch->branch_address = $request->address;
        $branch->branch_state = $request->state;
        $branch->branch_postcode = $request->postcode;
        $branch->branch_country = $request->country;

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
    public function show(Branch $branch)
    {
       $all_users = Role::where('name', 'admin')->orWhere('name', 'superadmin')->with('users')->get()->pluck('users')->flatten();
       $branch_users = $branch->users;
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
        $branch = Branch::find($request->branch_id);
        $user_id = json_decode($request->id);

        $branch->users()->sync($user_id);

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
        $branch = Branch::find($request->id);
        $branch->codename = $request->codename;
        $branch->branch_name = $request->name;
        $branch->branch_phone = $request->phone;
        $branch->branch_address = $request->address;
        $branch->branch_state = $request->state;
        $branch->branch_postcode = $request->postcode;
        $branch->branch_country = $request->country;

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
    public function destroy($id)
    {
        $branch = Branch::find($id);
        $branch->delete();
        Accessibility::where('branch_id' , $id)->delete();
       
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
            $branch = $user->branches;
        }
        else {
            $branch = Branch::select('branches.id', 'branches.codename', 'branches.branch_name')
                            ->leftJoin('lots' , 'lots.branch_id', '=', 'branches.id')
                            ->where('lots.user_id', $user->id)
                            ->get();
        }

       return $branch;
    }

    public function all_selector() {
        return Branch::all();
    }
}
