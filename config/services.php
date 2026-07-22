<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'waha' => [
        'endpoint' => env('WAHA_ENDPOINT', 'https://waha.armedia.id'),
        'url'      => env('WAHA_URL', 'http://waha:3000'), // Kept for backward compatibility
        'session'  => env('WAHA_SESSION', 'default'),
    ],

    'armedia' => [
        'cs_wa' => env('ARMEDIA_CS_WA', '628xxxxxxxxxx'),
        'cs_phone' => env('ARMEDIA_CS_PHONE', '0812-XXXX-XXXX'),
        'billing_reminder_day' => env('BILLING_REMINDER_DAY', 7),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    ],

];
