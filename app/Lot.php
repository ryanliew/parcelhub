<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Lot
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $category_id
 * @property string $name
 * @property string $volume
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereVolume($value)
 * @mixin \Eloquent
 * @property int|null $left_volume
 * @property string|null $expired_at
 * @property int $rental_duration
 * @property-read \App\Payment $payment
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereLeftVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot whereRentalDuration($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Payment[] $payments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OutboundProduct[] $outbounds_products_lots
 * @property float $price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Lot wherePrice($value)
 * @property-read mixed $usage
 */
class Lot extends Model
{
	protected $guarded = [];

    protected $appends = ['usage'];
	
    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function products(){
    	return $this->belongsToMany('App\Product')
                ->withPivot('quantity', 'incoming_quantity', 'outgoing_product');
    }

    public function category() {
    	return $this->belongsTo('App\Category');
    }

    public function payments() {
        return $this->belongsToMany('App\Payment');
    }

    public function setVolumeAttribute($value)
    {
        return $this->attributes['volume'] = $value * 100;
    }

    public function getUsageAttribute()
    {
        return ($this->volume - $this->left_volume) / 100 . '/' . $this->volume / 100;
    }
}
