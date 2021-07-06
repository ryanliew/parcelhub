<?php

namespace App;

use App\Product;
use App\Utilities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        return $this->attributes['volume'] = Utilities::convertMeterCubeToCentimeterCube($value);
    }

    public function getUsageAttribute()
    {
        return Utilities::convertCentimeterCubeToMeterCube(($this->volume - $this->left_volume)) . '/' . Utilities::convertCentimeterCubeToMeterCube($this->volume);
    }

    public function propagate_left_volume()
    {
        $used_volume = 0;
        foreach($this->products as $product)
        {
            $total_quantity = $product->pivot->quantity + $product->pivot->incoming_quantity - $product->pivot->outgoing_product;

            if( $product->pivot->quantity == 0 && $product->pivot->incoming_quantity == 0 && $product->pivot->outgoing_product == 0)
            {
                $this->products()->detach($product->id);
            }

            $used_volume += $total_quantity * $product->volume;
        }
        
        $this->update([ 'left_volume' => $this->volume - $used_volume ]);
    }

    public function check_can_deduct_product(Product $product, $quantity)
    {
        $lot_product = $this->products()->where('product_id', $product->id)->first();
        $new_quantity = $lot_product->pivot->quantity - $quantity;   

        if($new_quantity < 0) return false;

        return true;
    }

    public function deduct_product(Product $product, $quantity)
    {
        $lot_product = $this->products()->where('product_id', $product->id)->first();
        $new_quantity = $lot_product->pivot->quantity - $quantity;   

        if($new_quantity <= 0 
            && $lot_product->pivot->quantity <= 0 
            && $lot_product->pivot->outgoing_product <= 0)
        {
            $this->products()->detach($lot_product);
            Log::info("Lot detached: " . $this->id);
        }
        else
        {
            $this->products()->updateExistingPivot($product->id, ['quantity' => max($new_quantity, 0)]);
        }
    }

    public function deduct_incoming_product(Product $product, $quantity)
    {
        $lot_product = $this->products()->where('product_id', $product->id)->first();
        $new_quantity = $lot_product->pivot->incoming_quantity - $quantity;
        if($new_quantity <= 0 
            && $lot_product->pivot->quantity <= 0 
            && $lot_product->pivot->outgoing_product <= 0)
        {
            $this->products()->detach($lot_product);
            Log::info("Lot detached: " . $this->id);
        }
        else
        {
            $this->products()->updateExistingPivot($product->id, ['incoming_quantity' => max($new_quantity, 0)]);
        }
    }

    public function increase_incoming_product(Product $product, $quantity)
    {
        $lot_product = $this->fresh()->products()->where('product_id', $product->id)->first();
        if(is_null($lot_product))
        {
            $this->products()->attach($product->id);
            $lot_product = $this->products()->where('product_id', $product->id)->first();
        }

        $new_quantity = $lot_product->pivot->incoming_quantity + $quantity;
        Log::info("New quantity:" . $new_quantity);
        $this->products()->updateExistingPivot($product->id, ['incoming_quantity' => max($new_quantity, 0)]);
    }
}
