<?php

namespace App\Http\Controllers;

use Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('setting.page')->with('settings', Settings::getAll($cache = false));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'days_before_order' => 'required|integer',
            'rental_duration' => 'required|integer',
            'cancelation_number' => 'required',
            'cancelation_fee' => 'required|integer'
        ]);

        Settings::set('rental_duration', $request->get('rental_duration'));
        Settings::set('days_before_order', $request->get('days_before_order'));
        Settings::set('cancelation_number', $request->get('cancelation_number'));
        Settings::set('cancelation_fee', $request->get('cancelation_fee'));

        if($request->wantsJson())
        {
            return ['message' => 'Settings updated successfully', 'setting' => Settings::getAll()];
        }

        return redirect()->back()->withSuccess("Settings updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $settings)
    {
        //
    }
}
