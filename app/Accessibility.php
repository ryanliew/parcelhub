<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accessibility extends Model
{
    public function branches() {
        return $this->belongsToMany('App\Branch');
    }

    public function users(){
        return $this->belongsTo('App\User');
    }
}
