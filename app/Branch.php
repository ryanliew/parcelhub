<?php

namespace App;

use App\Scopes\BranchScope;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $connection = 'centralized_mysql';
    
    protected static function boot()
    {
        parent::boot();
    
        // static::deleting(function($branch) {
        //     $branch->access()->delete();
        // });
        static::updating(function ($branch) {
            if($branch->address_line_2 == '') {
                $branch->address = implode(", ", [$branch->address_line_1, $branch->city, $branch->postcode ." " . $branch->state]);
            }
            else {
                $branch->address = implode(", ", [$branch->address_line_1, $branch->address_line_2, $branch->city, $branch->postcode ." " . $branch->state]);
            }
        });

        static::creating(function ($branch) {
            if($branch->address_line_2) {
                $branch->address = implode(", ", [$branch->address_line_1, $branch->address_line_2, $branch->city, $branch->postcode ." " . $branch->state]);
            }
            else {
                $branch->address = implode(", ", [$branch->address_line_1, $branch->city, $branch->postcode ." " . $branch->state]);
            }
        });

        static::created(function ($branch) {
            $access_token = session()->get('access_token');

            $url = env('PARCELHUB_CENTER_URL');
            $client = new Client();

            try{
                $response = $client->request('POST', $url.'/api/branch/create/activityHistory', [
                    "headers" => [
                        'Accept' => 'application/json',
                        'Authorization' => "Bearer " . $access_token
                    ],
                    "form_params" => [
                        "email" => auth()->user()->email,
                        "branch_code" => $branch->code,
                        "create" => true,
                        "client_id" => env('PARCELHUB_CLIENT_ID'),
                    ]
                ]);
            }
            catch (\Exception $e) {
                $response = $e->getResponse();
            }
        });

        static::updated(function ($branch) {
            $access_token = session()->get('access_token');

            $url = env('PARCELHUB_CENTER_URL');
            $client = new Client();

            try{
                $response = $client->request('POST', $url.'/api/branch/create/activityHistory', [
                    "headers" => [
                        'Accept' => 'application/json',
                        'Authorization' => "Bearer " . $access_token
                    ],
                    "form_params" => [
                        "email" => auth()->user()->email,
                        "branch_code" => $branch->code,
                        'create' => false,
                        "client_id" => env('PARCELHUB_CLIENT_ID'),
                    ]
                ]);
            }
            catch (\Exception $e) {
                $response = $e->getResponse();
            }
        });
    
        static::addGlobalScope(new BranchScope);
    }
    public function lots() {
        return $this->hasMany('App\Lot', 'branch_code', 'code');
    }

    public function access() {
        return $this->hasMany('App\Accessibility', 'branch_code', 'code');
    }

    public function inbounds() {
        return $this->hasMany('App\Inbound', 'branch_code', 'code');
    }

    public function outbounds() {
        return $this->hasMany('App\Inbound', 'branch_code', 'code');
    }

}

