<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
	protected $guarded = [];

	protected $with = ['user', 'lot'];

    public function product()
    {
    	return $this->belongsTo('App\Product');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function lot()
    {
    	return $this->belongsTo('App\Lot');
    }
}
