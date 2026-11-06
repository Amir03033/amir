<?php

return [
    'name' => 'Amir Jebbari',
    'email' => env('SITE_EMAIL', 'hosseinjebbari@gmail.com'),
    // Hidden everywhere unless a real number is set in .env.
    'phone' => env('SITE_PHONE'),
    'phone_href' => env('SITE_PHONE_HREF'),
    'location' => 'Overijssel',
    'linkedin' => 'https://www.linkedin.com/in/amir-jebbari-19a63b330/',
    'github' => env('SITE_GITHUB'),
    'admin_email' => env('ADMIN_EMAIL', 'hosseinjebari@gmail.com'),

    'serverscope' => [
        'url' => env('SERVERSCOPE_URL'),
        'docs_url' => env('SERVERSCOPE_DOCS_URL'),
    ],

    // Sites probed by `php artisan site:check` for the live status panel.
    // Project demo URLs are added automatically.
    'status_targets' => array_filter([
        'ServerScope' => env('SERVERSCOPE_URL'),
    ]),
];
