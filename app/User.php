<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function outbounds(){
        return $this->hasMany('App\Outbound');
    }

    public function inbounds(){
        return $this->hasMany('App\Inbound');
    }

    public function lots(){
        return $this->hasMany('App\Lot');
    }

    public function payments(){
        return $this->hasMany('App\Payment');
    }

    public function roles(){
        return $this->belongsToMany('App\Role');
    }
}
