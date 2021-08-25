<?php

namespace App;

use App\Notifications\AccountApprovalNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\EntrustUserTrait;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Inbound[] $inbounds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lot[] $lots
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Outbound[] $outbounds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Payment[] $payments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User withRole($role)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read mixed $total_lot_left_volume
 * @property string|null $address
 * @property string|null $address_2
 * @property string|null $phone
 * @property string|null $state
 * @property string|null $postcode
 * @property string|null $country
 * @property-read \App\UserToken $tokens
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereState($value)
 * @property int $verified
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User admin()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereVerified($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Customer[] $customers
 */
class User extends Authenticatable
{
    use Notifiable;

    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['role_name', 'can_purchase'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function outbounds(){
        return $this->hasMany('App\Outbound');
    }

    public function inbounds(){
        return $this->hasMany('App\Inbound');
    }

    public function lots(){
        return $this->hasMany('App\Lot');
    }

    public function payments(){
        return $this->hasMany('App\Payment');
    }

    public function roles(){
        return $this->belongsToMany('App\Role');
    }

    public function products(){
        return $this->hasMany('App\Product');
    }

    public function tokens() {
        return $this->hasOne('App\UserToken');
    }

    public function customers()
    {
        return $this->hasMany('App\Customer');
    }

    public function subusers()
    {
        return $this->hasMany('App\User', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\User', 'parent_id');
    }

    public function branches() {
        return $this->belongsToMany('App\Branch', 'accessibilities')->withTimestamps();
    }

    public function scopeAdmin($query) {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', '=', 'admin');
        });
    }

    public function scopeSuperadmin($query) {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', '=', 'superadmin');
        });
    }

    public function getRoleNameAttribute()
    {
        return $this->roles()->first()->name;
    }

    public function getCanPurchaseAttribute()
    {
        return $this->address && $this->address_2 && $this->postcode && $this->phone && $this->state && $this->country;
    }

    public function getTotalLotLeftVolumeAttribute() {
        return $this->lots->sum('left_volume');
    }

    public function toggleApproval()
    {
        $this->update(['is_approved' => !$this->is_approved]);

        $this->notify(new AccountApprovalNotification($this));
        return $this;
    }
}
