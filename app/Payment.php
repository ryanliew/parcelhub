<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Payment
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $picture
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereUserId($value)
 * @mixin \Eloquent
 * @property int $lot_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lot[] $lot
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereLotId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lot[] $lots
 * @property float $price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment wherePrice($value)
 * @property string|null $invoice_slip
 * @property-read mixed $total_price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Payment whereInvoiceSlip($value)
 */
class Payment extends Model
{
	protected $guarded = [];
    protected $appends = ['total_price'];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = "cancelled";

    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function lots() {
        return $this->belongsToMany('App\Lot');
    }

    public function gateway()
    {
        return $this->belongsTo("App\PaymentGatewayDefinition");
    }

    public function getPictureAttribute($value)
    {
    	return asset(str_replace('public', 'storage', $value));
    }

    public function getTotalPriceAttribute()
    {
        $total = 0;

        foreach ($this->lots as $lot) {
            $total += $lot->price;
        }

        return $total;
    }
}
