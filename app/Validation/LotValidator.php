<?php
/**
 * Created by PhpStorm.
 * User: VT
 * Date: 2/18/2018
 * Time: 2:18 AM
 */

namespace App\Validation;

use App\Product;
use App\User;
use Illuminate\Validation\Validator;

class LotValidator extends Validator
{
    public function validateLotSpace($attribute, $value, $parameters)
    {
        $total = 0;

        if(!is_array($value)) { return true; }

        foreach ($value as $v) {

            // Product's ID and quantity are must set to required
            // In order to calculate the total products volume
            if(!isset($v['id']) || !isset($v['quantity'])) {
                return true;
            }

            $product = Product::find($v['id']);

            $total += $product->volume * $v['quantity'];

        }

        // The total products volume after multiply by its quantity
        // must larger than the available lots volume
        return $total > User::find(auth()->id())->total_lot_left_volume;
    }
}