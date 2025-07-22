<?php

return [
    'api_url' => env('MONEYFUSION_API_URL', 'YOUR_API_URL_FROM_DASHBOARD'),
    'payment_status_url' => env('MONEYFUSION_PAYMENT_STATUS_URL', 'https://www.pay.moneyfusion.net/paiementNotif/'),
    'webhook_secret' => env('MONEYFUSION_WEBHOOK_SECRET', null), // Si MoneyFusion supporte les signatures de webhook
    'routes' => [
        'prefix' => 'moneyfusion',
        'middleware' => ['web'],
        'webhook_path' => 'webhook',
        'callback_path' => 'callback',
    ],
    'views' => [
        'layout' => 'moneyfusion::layouts.app',
    ],

    'billing' => [
        'company_name' => env('APP_NAME', 'Your Company'),
        'company_address' => '123 Rue de l\'Exemple',
        'company_city_zip' => '75000 Paris',
        'company_country' => 'France',
        'company_email' => 'contact@yourcompany.com',
        'company_phone' => '+33 1 23 45 67 89',
        'invoice_prefix' => 'INV-',
    ],
    'pdf_driver' => env('MONEYFUSION_PDF_DRIVER', 'dompdf'), // 'dompdf' ou 'snappy'
];