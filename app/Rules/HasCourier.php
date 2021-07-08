<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Courier;

class HasCourier implements Rule
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
        $courier = Courier::where('name', 'LIKE', '%' . $value . '%')->first();

        return !is_null($courier);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid courier. Please check your Courier :input';
    }
}
