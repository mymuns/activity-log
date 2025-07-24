<?php

return [
    'enabled' => true,

    'log_channel' => env('ACTIVITY_LOG_CHANNEL', 'database'), // database, daily, stack, elastic

    'mask_request_keys' => ['password', 'token'],

    'max_body_length' => 2000,
];

