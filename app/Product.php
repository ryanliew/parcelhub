<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Product
 *
 * @property int $id
 * @property int|null $lot_id
 * @property string $name
 * @property float $height
 * @property float $length
 * @property float $width
 * @property string $sku
 * @property string|null $picture
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Lot|null $lot
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereLotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Product whereWidth($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
	protected $guarded = [];
	
    public function lots(){
    	return $this->belongsToMany('App\Lot');
    }

    public function inbounds(){
    	return $this->belongsToMany('App\Inbound');
    }

    public function outbounds(){
    	return $this->belongsToMany('App\Outbound');
    }

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
