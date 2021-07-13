<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Product;

class ProductHasLot implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $product = Product::where('sku', $value)
                    ->where('status', 'true')
                    ->where('user_id', auth()->id())
                    ->first();
        
        if(!$product) {
            return false;
        }

        return $product->lots()->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid product. Please check your products (:input) and lots';
    }
}
