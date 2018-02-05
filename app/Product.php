<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $guarded = [];
	
    public function lot(){
    	return $this->belongsTo('App\Lot');
    }
}
