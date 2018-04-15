<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingNumber extends Model
{
	protected $guarded = [];
	
    public function outbound()
    {
    	return $this->belongsTo('App\Outbound');
    }
}
