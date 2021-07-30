<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function lots() {
        return $this->hasMany('App\Lot');
    }

    public function users() {
        return $this->belongsToMany('App\User', 'accessibilities')->withTimestamps();
    }

    public function inbounds() {
        return $this->hasMany('App\Inbound');
    }

    public function outbounds() {
        return $this->hasMany('App\Inbound');
    }
}
