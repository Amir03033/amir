<?php

// Name, email, location and LinkedIn come from config/seo.php (the single source of truth).
$seo = require __DIR__.'/seo.php';

return [
    'name' => $seo['name'],
    // Public address everywhere (footer, contact, JSON-LD, CV). Not read from .env, so no personal address
    // can leak back in. Contact notifications are sent here too.
    'email' => $seo['email'],
    // Hidden everywhere unless a real number is set in .env.
    'phone' => env('SITE_PHONE'),
    'phone_href' => env('SITE_PHONE_HREF'),
    'location' => implode(' & ', $seo['locations']),
    'linkedin' => $seo['same_as'][0],  // the same LinkedIn profile jebbari.nl links to
    'github' => env('SITE_GITHUB'),
    'admin_email' => env('ADMIN_EMAIL', 'hosseinjebari@gmail.com'),

    // Raw page views older than this are deleted nightly; daily totals are kept forever.
    'visits_retention_days' => (int) env('VISITS_RETENTION_DAYS', 90),

    'booking_url' => env('SITE_BOOKING_URL'),

    // The agency. Only linked from the footer and the about section.
    'jebbari' => [
        'url' => $seo['company']['url'],
    ],

    // These can also be set in Filament under "Links & instellingen", which overrides .env.
    'serverscope' => [
        'url' => env('SERVERSCOPE_URL'),
        'docs_url' => env('SERVERSCOPE_DOCS_URL'),
        'status_url' => env('SERVERSCOPE_STATUS_URL'),
    ],

    // Sites probed by `php artisan site:check` for the live status panel.
    // Project demo URLs are added automatically.
    'status_targets' => array_filter([
        'ServerScope' => env('SERVERSCOPE_URL'),
        // Jebbari is measured too, for the Ping experiment on the home page.
        'Jebbari' => env('JEBBARI_URL', 'https://jebbari.nl'),
    ]),
];
