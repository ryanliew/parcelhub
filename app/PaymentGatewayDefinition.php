<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayDefinition extends Model
{
    protected $appends = ['image_path_display'];

    public function scopeActive($query)
    {
        return $query->where("active", 1);
    }

    public function getImagePathDisplayAttribute()
    {
        return '/storage/' . $this->image_path;
    }
}
