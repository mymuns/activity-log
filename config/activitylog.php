<?php

return [
    'enabled' => true,

    'log_channel' => env('ACTIVITY_LOG_CHANNEL', 'database'), // database, daily, stack, elastic

    'mask_request_keys' => ['password', 'token'],

    'max_body_length' => 2000,
    'hide_response_fields' => [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'refresh_token',
        'credit_card_number',
        'cvv',
        // istediğin diğer alanlar
    ],
];

