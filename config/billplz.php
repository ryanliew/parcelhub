<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Billplz Secret Key
    |--------------------------------------------------------------------------
    |
    | Secret Key for BillPlz
    |
    */

    'secret' => env('BILLPLZ_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Billplz Callback Path
    |--------------------------------------------------------------------------
    |
    | Redirect path when billplz send result back to server from their portal (POST Request)
    |
    */
    'callback_path' => '/payment/billplzResponse',

    /*
    |--------------------------------------------------------------------------
    | Billplz Redirect Path
    |--------------------------------------------------------------------------
    |
    | Redirect path when billplz send result back to server from their portal (GET Request)
    | Will overwrite the callback path
    |
    */
    'redirect_path' => '/payment/billplzResponse',
];