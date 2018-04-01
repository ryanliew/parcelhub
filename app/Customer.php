<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Customer
 *
 * @property int $id
 * @property string $customer_name
 * @property string $customer_address
 * @property string|null $customer_address_2
 * @property string $customer_phone
 * @property string $customer_postcode
 * @property string $customer_state
 * @property string $customer_country
 * @property int|null $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerPostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomerState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereUserId($value)
 * @mixin \Eloquent
 */
class Customer extends Model
{
	protected $guarded = ['id', 'user_id'];

    public function users()
    {
    	return $this->belongsTo('App\User');
    }
}
