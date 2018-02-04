<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lot_Category extends Model
{
    public function lot(){
    	return $this->belongsTo('App\Lot');
    }
}
