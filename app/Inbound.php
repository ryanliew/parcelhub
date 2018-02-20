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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property string $process_status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\InboundProduct[] $products_with_lots
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound awaitingArrival()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound canceled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound completed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound processing()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereProcessStatus($value)
 */
class Inbound extends Model
{
	protected $guarded = [];
	
    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function products(){
    	return $this->belongsToMany('App\Product')->withPivot('quantity')->withTimestamps();
    }

    public function products_with_lots(){
        return $this->hasMany('App\InboundProduct');
    }

    public function scopeAwaiting_arrival($query){
    	return $query->where('process_status', 'awaiting_arrival');
    }

    public function scopeProcessing($query){
    	return $query->where('process_status', 'processing');
    }

    public function scopeCompleted($query){
    	return $query->where('process_status', 'completed');
    }

    public function scopeCanceled($query){
    	return $query->where('process_status', 'canceled');
    }

}
