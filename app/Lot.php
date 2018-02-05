<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
	protected $guarded = [];
	
    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function products(){
    	return $this->hasMany('App\Product');
    }

    public function category(){
    	return $this->belongsTo('App\Category');
    }
}
