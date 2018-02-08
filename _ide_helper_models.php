<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
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
 */
	class Category extends \Eloquent {}
}

namespace App{
/**
 * App\Courier
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Outbound $outbound
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Courier whereUpdatedAt($value)
 */
	class Courier extends \Eloquent {}
}

namespace App{
/**
 * App\Inbound
 *
 * @property int $id
 * @property int $user_id
 * @property string $product
 * @property int $quantity
 * @property string $arrival_date
 * @property int $total_carton
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereArrivalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereTotalCarton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Inbound whereUserId($value)
 */
	class Inbound extends \Eloquent {}
}

namespace App{
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
 */
	class Lot extends \Eloquent {}
}

namespace App{
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
 */
	class Outbound extends \Eloquent {}
}

namespace App{
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
 */
	class Payment extends \Eloquent {}
}

namespace App{
/**
 * App\Product
 *
 * @property int $id
 * @property int $lot_id
 * @property string $name
 * @property float $height
 * @property float $length
 * @property float $width
 * @property string $sku
 * @property string|null $picture
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Lot $lot
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
 */
	class Product extends \Eloquent {}
}

namespace App{
/**
 * App\Role
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App{
/**
 * App\Setting
 *
 * @property int $id
 * @property string $rental_duration
 * @property string $days_before_order
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereDaysBeforeOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereRentalDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereUpdatedAt($value)
 */
	class Setting extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
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
 */
	class User extends \Eloquent {}
}

