<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    public function outbound(){
    	return $this->belongsTo('App\Outbound');
    }
}
