<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Settings
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings daysBeforeOrder()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings rentalDuration()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereValue($value)
 * @mixin \Eloquent
 * @property string $setting_key
 * @property string $setting_value
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereSettingKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings whereSettingValue($value)
 */
class Settings extends Model
{
    protected $fillable = ['setting_key', 'setting_value'];
}
