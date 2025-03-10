<?php

return [
    'payu' => [
        'merchant_key' => env('PAYU_MERCHANT_KEY'),
        'salt' => env('PAYU_SALT'),
        'auth_header' => env('PAYU_AUTH_HEADER'),
        'test_mode' => env('PAYU_TEST_MODE', true),
    ],

    'przelewy24' => [
        'merchant_id' => env('PRZELEWY24_MERCHANT_ID'),
        'pos_id'      => env('PRZELEWY24_POS_ID'),
        'crc_key'     => env('PRZELEWY24_CRC_KEY'),
        'api_key'     => env('PRZELEWY24_API_KEY'),
        'test_mode'   => env('PRZELEWY24_TEST_MODE', true),
        'return'    => env('PRZELEWY24_URL_RETURN', 'p24.status'),
        'status'    => env('PRZELEWY24_URL_STATUS', 'p24.status'),
    ],
];
