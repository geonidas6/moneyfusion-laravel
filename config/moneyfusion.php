<?php

// config for Sefako/Moneyfusion
return [
    'api_url' => env('MONEYFUSION_API_URL', 'https://www.pay.moneyfusion.net'),
    'api_key' => env('MONEYFUSION_API_KEY', ''),
    'make_payment_api_url' => env('MONEYFUSION_MAKE_PAYMENT_API_URL', 'https://www.pay.moneyfusion.net/makePayment'),
    'environment' => env('MONEYFUSION_ENV', 'production'),
];
