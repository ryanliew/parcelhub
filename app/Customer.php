<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	protected $guarded = ['id', 'user_id'];

    public function users()
    {
    	return $this->belongsTo('App\User');
    }
}
