<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | InterActive QRIS Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi QRIS InterActive
    | Dokumentasi: https://qris.interactive.co.id/api-doc/
    |
    */
    'qris' => [
        'api_key'       => env('QRIS_API_KEY', ''),
        'merchant_id'   => env('QRIS_MERCHANT_ID', ''),
        'nmid'          => env('QRIS_NMID', ''),
        'merchant_name' => env('QRIS_MERCHANT_NAME', 'HM TOUR AND TRAVEL'),
        'base_url'      => env('QRIS_BASE_URL', 'https://qris.interactive.co.id/restapi/qris/show_qris.php'),
        'check_url'     => env('QRIS_CHECK_URL', 'https://qris.interactive.co.id/restapi/qris/checkpaid_qris.php'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fonnte WhatsApp API
    |--------------------------------------------------------------------------
    */
    'fonnte' => [
        'token' => env('FONNTE_TOKEN', ''),
        'url'   => env('FONNTE_URL', 'https://api.fonnte.com/send'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Cloud Vision OCR
    |--------------------------------------------------------------------------
    */
    'google_vision' => [
        'api_key' => env('GOOGLE_VISION_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 (GA4) Data API
    |--------------------------------------------------------------------------
    | credentials_path: path ke file JSON service account
    | property_id    : GA4 Property ID (angka, bukan G-xxx)
    |--------------------------------------------------------------------------
    */
    'google_analytics' => [
        'credentials_path' => env('GA4_CREDENTIALS_PATH', storage_path('app/google-analytics-credentials.json')),
        'property_id'      => env('GA4_PROPERTY_ID', ''),
    ],

];
