<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function states()
    {
        return $this->hasMany("App\State");
    }

    public function scopeActive($query)
    {
        return $query->where("enabled", 1);
    }
}
