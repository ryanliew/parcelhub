<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UserToken
 *
 * @property int $user_id
 * @property string $token
 * @property string|null $expire_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserToken whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserToken whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserToken whereExpireAt($value)
 */
class UserToken extends Model
{
    protected $guarded = ['user_id'];

    protected $dates = ['expire_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function getIsExpiredAttribute()
    {
    	return $this->expire_at->lt(Carbon::now());
    }
}
