<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'regofis' => [
        'token' => env('REGOFIS_TOKEN'),
        'api_url' => env('REGOFIS_API_URL', 'https://edu.regofis.uz/api'),
    ],

    'hemis' => [
        'token' => env('API_HEMIS'),
        'user_url' => env('HEMIS_USER_URL', 'https://hemis.karsu.uz'),
        'student_url' => env('HEMIS_STUD_URL', 'https://student.karsu.uz'),
        'client_id' => env('HEMIS_CLIENT_ID'),
        'client_secret' => env('HEMIS_CLIENT_SECRET'),
        'redirect_user' => env('HEMIS_REDIRECT_URI_USER'),
        'redirect_stud' => env('HEMIS_REDIRECT_URI_STUD'),
    ],
];
