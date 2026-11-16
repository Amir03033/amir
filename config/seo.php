<?php

/*
|--------------------------------------------------------------------------
| The core entity: one source of truth for who Amir is, used everywhere
| (meta tags, JSON-LD, OG images, llms.txt, footer, CV). Keep it identical
| to jebbari.nl. See SEO.md and SEO-JEBBARI.md.
|--------------------------------------------------------------------------
*/

return [
    'name' => 'Amir Jebbari',            // as on https://jebbari.nl/nl/over-mij
    'given_name' => 'Amir',
    'family_name' => 'Jebbari',

    'site_name' => 'amirs.life',

    // Canonical origin. In production every generated URL uses it (see AppServiceProvider),
    // and .htaccess sends www/http to it with a 301.
    'url' => env('SEO_CANONICAL_URL', 'https://amirs.life'),
    'force_canonical_host' => env('SEO_FORCE_CANONICAL_HOST', env('APP_ENV') === 'production'),

    // Stable JSON-LD identifiers.
    'person_id' => 'https://amirs.life/#amir',
    'website_id' => 'https://amirs.life/#website',

    'role' => [
        'nl' => 'Oprichter van Jebbari',
        'en' => 'Founder of Jebbari',
    ],
    'job_title' => [
        'nl' => 'Oprichter',
        'en' => 'Founder',
    ],

    // The canonical sentence. Same wording as jebbari.nl (Person description and llms.txt).
    'description' => [
        'nl' => 'Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle.',
        'en' => 'Amir Jebbari is the founder of Jebbari, based in Amsterdam and Zwolle.',
    ],

    'locations' => ['Amsterdam', 'Zwolle'],
    'country' => 'NL',

    'email' => 'info@jebbari.nl',

    'company' => [
        'name' => 'Jebbari',
        'url' => 'https://jebbari.nl',
        'id' => 'https://jebbari.nl/#org',          // the @id jebbari.nl uses for its organisation
        'person_id' => 'https://jebbari.nl/#amir',  // the @id jebbari.nl uses for Amir (linked via sameAs)
        'about' => [
            'nl' => 'https://jebbari.nl/nl/over-mij',
            'en' => 'https://jebbari.nl/en/about',
        ],
        // Real flows on jebbari.nl (checked 2026-11-16). The wizard is linked at step 1 directly, because
        // the /nl/aanvraag → /stap/1 redirect drops query strings (and with them the UTM parameters).
        'request' => [
            'nl' => 'https://jebbari.nl/nl/aanvraag/stap/1',
            'en' => 'https://jebbari.nl/en/request/step/1',
        ],
        'contact' => [
            'nl' => 'https://jebbari.nl/nl/contact',
            'en' => 'https://jebbari.nl/en/contact',
        ],
        // The services jebbari.nl offers (slugs per language), in the order of /diensten.
        'services' => [
            'website' => ['nl' => 'website-ontwikkeling', 'en' => 'website-development'],
            'webapp' => ['nl' => 'maatwerk-webapplicaties', 'en' => 'custom-web-applications'],
            'ecommerce' => ['nl' => 'e-commerce', 'en' => 'e-commerce'],
            'api' => ['nl' => 'api-ontwikkeling-systeemintegraties', 'en' => 'api-development-integrations'],
            'maintenance' => ['nl' => 'onderhoud-hosting', 'en' => 'maintenance-hosting'],
            'design' => ['nl' => 'uiux-digitaal-productontwerp', 'en' => 'uiux-digital-product-design'],
            'seo' => ['nl' => 'seo-websiteoptimalisatie', 'en' => 'seo-performance-optimization'],
        ],
    ],

    // UTM parameters on every link from amirs.life to jebbari.nl (utm_campaign = where the link sits).
    'utm' => ['utm_source' => 'amirs.life', 'utm_medium' => 'portfolio'],

    // Profiles that are about this same person. Add the rest when known (GitHub, etc.).
    'same_as' => array_values(array_filter([
        'https://www.linkedin.com/in/amir-j-a3948b3a9',  // same profile as on jebbari.nl
        'https://jebbari.nl/nl/over-mij',
        'https://jebbari.nl/en/about',
        env('SITE_GITHUB'),                               // PLACEHOLDER: GitHub profile URL
    ])),

    // Only what is real and shown in the work on this site and on jebbari.nl.
    'knows_about' => [
        'Web application development',
        'Laravel',
        'PHP',
        'Filament',
        'Livewire',
        'Vue.js',
        'SaaS',
        'Server monitoring',
    ],

    // Pages that must never be indexed (X-Robots-Tag: noindex). Patterns for Request::is().
    'noindex' => ['amir', 'amir/*', 'login', 'two-factor*', 'livewire/*', '_styleguide', 'ping', 'status', 'en/status'],
];
