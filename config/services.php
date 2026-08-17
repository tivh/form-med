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

    'glpi' => [
        // Legacy REST API base path (works with a per-user personal token).
        // The newer "api.php/v2" endpoint is OAuth2-only and is NOT compatible with GLPI_USER_TOKEN.
        'api_base_url' => env('GLPI_API_BASE_URL'),
        'app_token' => env('GLPI_APP_TOKEN'),
        'user_token' => env('GLPI_USER_TOKEN'),
        'front_base_url' => env('GLPI_FRONT_BASE_URL'),
        'feed_statuses' => env('GLPI_FEED_STATUSES', '1,2,3,4'),
        'feed_category_name' => env('GLPI_FEED_CATEGORY_NAME', ''),
    ],

];
