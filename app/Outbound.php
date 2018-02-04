<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
	protected $guarded = [];
	
    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function courier(){
    	return $this->hasOne('App\courier');
    }
}
