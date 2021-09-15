<?php

namespace App;

use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $connection = 'centralized_mysql';

    public function lots() {
        return $this->hasMany('App\Lot', 'branch_code', 'code');
    }

    public function access() {
        return $this->hasMany('App\Accessibility', 'branch_code', 'code');
    }

    public function inbounds() {
        return $this->hasMany('App\Inbound', 'branch_code', 'code');
    }

    public function outbounds() {
        return $this->hasMany('App\Inbound', 'branch_code', 'code');
    }

    protected static function boot()
    {
        parent::boot();

        // static::deleting(function($branch) {
        //     $branch->access()->delete();
        // });

        static::addGlobalScope(new BranchScope);
    }
}
