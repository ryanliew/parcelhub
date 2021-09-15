<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accessibility extends Model
{
    protected $connection = 'mysql';

    protected $fillable = ['user_id', 'branch_id', 'branch_code'];

    public function branches() {
        return $this->belongsTo('App\Branch', 'branch_code', 'code');
    }

    public function users(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
