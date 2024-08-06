<?php

use Illuminate\Support\Facades\Facade;


return [
    'merchant_id' => env("MIDTRANS_MERCHANT_ID"),
    'server_key' => env("MIDTRANS_SERVER_KEY"),
    'client_key' => env("MIDTRANS_CLIENT_KEY"),
    'isProduction' => env("MIDTRANS_IS_PRODUCTION"),
];
