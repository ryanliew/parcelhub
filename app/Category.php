<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function lots() {
    	return $this->hasMany('App\Lot');
    }
}
