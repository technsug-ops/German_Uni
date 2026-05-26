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

    'gemini' => [
        'key'   => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

    'elevenlabs' => [
        'key'      => env('ELEVENLABS_API_KEY'),
        'voice_id' => env('ELEVENLABS_VOICE_ID', '9BWtsMINqrJLrRacOk9x'), // Aria (multilingual default)
        'model'    => env('ELEVENLABS_MODEL', 'eleven_multilingual_v2'),
    ],

    'image' => [
        // Provider priority — sırayla dene, ilki fail ederse sonrakine düş.
        // nano_banana = Gemini 2.5 Flash Image (paid tier gerek)
        // pollinations = ücretsiz FLUX (key gerek yok)
        'providers' => array_filter(array_map('trim', explode(',', env('IMAGE_PROVIDERS', 'nano_banana,pollinations')))),
    ],

    'partner' => [
        'base_url'       => env('PARTNER_API_BASE_URL'),
        'api_key'        => env('PARTNER_API_KEY'),
        'auth_header'    => env('PARTNER_API_AUTH_HEADER', 'X-API-Key'),
        'timeout'        => (int) env('PARTNER_API_TIMEOUT', 60),
        'page_size'      => (int) env('PARTNER_API_PAGE_SIZE', 200),
        'webhook_secret' => env('PARTNER_WEBHOOK_SECRET'),
        'sync_schedule'  => env('PARTNER_SYNC_SCHEDULE', 'daily'),
    ],

];
