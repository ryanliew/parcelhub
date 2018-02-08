<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Courier
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Outbound $outbound
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Courier extends Model
{
	protected $guarded = [];
	
    public function outbound(){
    	return $this->belongsTo('App\Outbound');
    }
}
