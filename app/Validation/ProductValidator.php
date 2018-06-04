<?php
/**
 * Created by PhpStorm.
 * User: VT
 * Date: 2/23/2018
 * Time: 4:10 AM
 */

namespace App\Validation;

use App\Product;
use Illuminate\Validation\Validator;

class ProductValidator extends Validator
{
    public function validateProductExist($attribute, $value, $parameters) {

        if(!isset($value['id'])) {
            return false;
        }

        $product = \Auth::user()->products()->find($value['id']);

        if(auth()->user()->hasRole('admin'))
        {
            $product = Product::find($value['id']);
        }

        return isset($product);
    }

    public function replaceProductExist($message, $attribute, $rule, $parameters) {
        return $message;
    }

    public function validateProductStock($attribute, $value, $parameters) {

        // Product's ID and quantity are must set to required
        // In order to calculate the total products volume
        if(!is_array($value) || !isset($value['id']) || !isset($value['quantity'])) {
            return false;
        }

        $product = \Auth::user()->products->find($value['id']);

        if(auth()->user()->hasRole('admin'))
        {
            $product = Product::find($value['id']);
        }

        return $value['quantity'] <= $product->total_usable_quantity;
    }

    public function replaceProductStock($message, $attribute, $rule, $parameters) {
        $value = $this->getValue($attribute);

        $product = Product::find($value['id']);
        $name = $product->name;
        $number = $product->total_usable_quantity;

        return str_replace([':number'], $number, str_replace([':product'], $name, $message));
    }
}