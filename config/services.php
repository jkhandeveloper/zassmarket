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

    'ai_suggestions' => [
        'url' => env('AI_SUGGESTION_URL', env('AI_SERVICE_URL', 'http://127.0.0.1:8000/suggestions')),
        'timeout' => env('AI_SERVICE_TIMEOUT', 5),
    ],

    'stripe' => [
        'enabled' => env('STRIPE_ENABLED', false),
        'secret' => env('STRIPE_SECRET_KEY'),
        'currency' => env('STRIPE_CURRENCY') ?: 'usd',
    ],

    'bank_transfer' => [
        'bank_name' => env('BANK_NAME'),
        'account_title' => env('BANK_ACCOUNT_TITLE'),
        'account_number' => env('BANK_ACCOUNT_NUMBER'),
        'iban' => env('BANK_IBAN'),
        'swift' => env('BANK_SWIFT'),
        'branch' => env('BANK_BRANCH'),
        'instructions' => env('BANK_TRANSFER_INSTRUCTIONS', 'Please upload your bank transfer receipt after payment.'),
    ],

];
