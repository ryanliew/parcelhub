<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\InboundProduct
 *
 * @property int $inbound_id
 * @property int $product_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $quantity
 * @property-read mixed $lots_name
 * @property-read \App\Inbound $inbound
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lot[] $lots
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InboundProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InboundProduct whereInboundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InboundProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InboundProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InboundProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\InboundProduct whereId($value)
 */
class InboundProduct extends Model
{
	protected $guarded = [];

	protected $table = 'inbound_product';

    protected $appends = ["lots_name"];
	
    public function lots(){
    	return $this->belongsToMany('App\Lot')->withPivot('expiry_date', 'remark', 'quantity_received')->withTimestamps();
    }

    public function inbound(){
    	return $this->belongsTo('App\Inbound');
    }

    public function product(){
    	return $this->belongsTo('App\Product');
    }

    // Accessor
    public function getLotsNameAttribute()
    {
        return $this->lots->pluck('name')->implode(', ');
    }
}

