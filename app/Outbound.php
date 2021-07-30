<?php

namespace App;

use DB;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * App\Outbound
 *
 * @property int $id
 * @property int $user_id
 * @property int $courier_id
 * @property int $product
 * @property string $recipient_name
 * @property string $recipient_address
 * @property int $insurance
 * @property float $amount_insured
 * @property int $dangerous
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Courier $courier
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereAmountInsured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereCourierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereDangerous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereInsurance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereRecipientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property int $quantity
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereQuantity($value)
 * @property string $process_status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound canceled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound completed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound delivering()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound processing()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereProcessStatus($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OutboundProduct[] $outbounds_products_lots
 * @property string|null $recipient_address_2
 * @property string|null $recipient_phone
 * @property string|null $recipient_state
 * @property string|null $recipient_postcode
 * @property string|null $recipient_country
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereRecipientAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereRecipientCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereRecipientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereRecipientPostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereRecipientState($value)
 * @property string|null $invoice_slip
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Outbound whereInvoiceSlip($value)
 * @property-read mixed $product_lot
 */
class Outbound extends Model
{
	protected $guarded = ['id', 'user_id', 'amount_insured', 'outbound_products', 'status', 'customer_id'];
    
    protected $with = ['tracking_numbers'];

    protected $appends = ['display_no'];
    
    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function products() {
        return $this->belongsToMany('App\Product')->withPivot('quantity', 'lot_id', 'remark', 'unit_value', 'total_value', 'weight', 'manufacture_country');
    }

    public function branch(){
        return $this->belongsTo('App\Branch');
    }

    public function courier() {
    	return $this->belongsTo('App\Courier');
    }

    public function tracking_numbers()
    {
        return $this->hasMany('App\TrackingNumber');
    }

    public function getTotalProductQuantityAttribute($product_id) {
        $total = 0;

        $products = $this->products()->where('product_id', $product_id)->get();

        foreach ($products as $product) {
            $total += $product->pivot->quantity;
        }

        return $total;
    }

    public function getProductLocationInfoAttribute($product_id) {
        return DB::table('outbound_product')
            ->join('lots', 'outbound_product.lot_id','lots.id')
            ->where('outbound_id', $this->id)
            ->where('product_id', $product_id)
            ->select('lots.name', 'outbound_product.quantity')
            ->get();
    }

    public function getDisplayNoAttribute()
    {
        return $this->PREFIX() . sprintf("%07d", $this->id);
    }

    public function scopeProcessing($query) {
        return $query->where('process_status', 'processing');
    }

    public function scopeDelivering($query) {
        return $query->where('process_status', 'delivering');
    }

    public function scopeCompleted($query) {
        return $query->where('process_status', 'completed');
    }

    public function scopeCanceled($query) {
        return $query->where('process_status', 'canceled');
    }

    // Methods

    public function updateTrackingNumber($tracking_numbers)
    {
        $numbers = explode(";", $tracking_numbers);

        // Clear out number that are no longer needed
        $this->tracking_numbers()->whereNotIn('number', $numbers)->delete();

        // Find out what numbers we need to add in
        $difference = collect($numbers)->diff($this->tracking_numbers->pluck('number'));

        $this->tracking_numbers()->createMany(array_map(
                                                    function($value){ 
                                                        return ["number" => $value];
                                                    }, 
                                                    $difference->all()));
    }

    public function totalQuantity(){
        $totalQuantity = 0;
        foreach($this->products as $outboundProduct){
            $totalQuantity += $outboundProduct->pivot->quantity;
        }
        return $totalQuantity;
    }

    public function totalValue(){
        $totalPrice = 0;
        foreach($this->products as $outboundProduct){
            $totalPrice += $outboundProduct->pivot->total_value;
        }
        return $totalPrice;
    }

    public function notify($outboundNotification) {

        $admins = $this->branch->users;
        
        foreach($admins as $admin) {
            $admin->notify($outboundNotification);
        }
        
        User::superadmin()->first()->notify($outboundNotification);

        auth()->user()->notify($outboundNotification);
    }

    // Static methods
    public static function PREFIX()
    {
        return 'PLA';
    }
    
}
