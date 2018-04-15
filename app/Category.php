<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilities;
/**
 * App\Category
 *
 * @property int $id
 * @property string $name
 * @property float $volume
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lot[] $lots
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereVolume($value)
 * @mixin \Eloquent
 * @property float $price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category wherePrice($value)
 */
class Category extends Model
{
    protected $guarded = [];

    public function lots() {
    	return $this->hasMany('App\Lot');
    }

    public function setVolumeAttribute($value)
    {
    	return $this->attributes['volume'] = Utilities::convertMeterCubeToCentimeterCube($value);
    }
}
