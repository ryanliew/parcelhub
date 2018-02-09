<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Inbound
 *
 * @property int $id
 * @property int $user_id
 * @property string $product
 * @property int $quantity
 * @property string $arrival_date
 * @property int $total_carton
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereArrivalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereTotalCarton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereUserId($value)
 * @mixin \Eloquent
 */
class Inbound extends Model
{
	protected $guarded = [];
	
    public function user(){
    	return $this->belongsTo('App\User');
    }
}
