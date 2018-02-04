<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lot_Category extends Model
{
	protected $guarded = [];
	
    public function lot(){
    	return $this->belongsTo('App\Lot');
    }
}
