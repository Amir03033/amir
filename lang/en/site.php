<?php

// Copy for the redesigned site. Lines marked DRAFT are written by Claude in Amir's voice and wait for his approval.
// Facts that are missing are not invented: they are placeholders (see the <x-ph> component and DEPLOY.md).

return [
    'meta' => [
        'title' => 'Amir Jebbari — founder of Jebbari, builder of ServerScope',
        'description' => 'The personal site of Amir Jebbari: founder of the web agency Jebbari in Zwolle and Amsterdam, builder of ServerScope, and a student. Work, notes and a place to talk back.',
    ],

    'hero' => [
        // DRAFT
        'lines' => ['I build websites,', 'and sometimes the tools', 'that watch over them.'],
        'role' => 'Founder of Jebbari · Zwolle & Amsterdam',
        'time' => 'Amsterdam',
        'stamp' => 'Leave a stamp',
    ],

    'work' => [
        'screenshot_alt' => 'Screenshot of :name',
        'label' => 'Work',
        'title' => 'Things I’ve built.', // DRAFT
        'case' => 'Read the case',
        'live' => 'Live site',
        'more' => 'More work',
        'all' => 'All projects',
        'empty' => 'Projects appear here once they’re live.',
        'no_image' => 'Screenshot coming',
        'role' => 'Role',
        'year' => 'Year',
        'stack' => 'Stack',
        'link' => 'Link',
        'index_title' => 'Work',
        'index_lead' => 'Everything I’ve built that’s live, with what I did on each project and what I used.',
        'filter_all' => 'All',
        'filter_label' => 'Filter by stack',
    ],

    'case' => [
        'crumb' => 'Work',
        'problem' => 'The problem',
        'solution' => 'What I built',
        'result' => 'The result',
        'about' => 'About the project',
        'gallery' => 'Screenshots',
        'visit' => 'Visit the live site',
        'source' => 'Source code',
        'uptime' => 'Uptime, last 30 days',
        'uptime_note' => 'Measured by this site every 10 minutes.',
        'since' => 'measured since :date',
        'previous' => 'Previous',
        'next' => 'Next',
        'missing_case' => 'problem, approach and result',
        'missing_meta' => 'year and role',
        'image_n' => ':name, screenshot :n',
    ],

    'about' => [
        'label' => 'About',
        'title' => 'Student. Founder. Builder.', // DRAFT
        'photo_alt' => 'Amir Jebbari on a bridge in Salzburg',
        // DRAFT, built from facts already on the old site and in the brief.
        'text' => 'I’m Amir. I run Jebbari, a web agency in Zwolle and Amsterdam, and I’m also a student.',
        'text_2' => 'Next to client work I build my own products. ServerScope is the biggest one: monitoring for servers and domains. It taught me what it takes to run software, not just write it.',
        'facts' => [
            'agency' => 'Agency',
            'cities' => 'Cities',
            'product' => 'Own product',
            'study' => 'Studying',
            'since' => 'Jebbari since',
        ],
        'cities_value' => 'Zwolle & Amsterdam',
        'to_jebbari' => 'Visit Jebbari',
        'cv' => 'View my CV',
    ],

    'testimonials' => [
        'label' => 'What others say',
    ],

    'certificates' => [
        'label' => 'Certificates',
        'title' => 'Things I’ve finished.',
        'all' => 'All certificates',
        'view' => 'View',
        'empty' => 'No certificates added yet.',
        'lead' => 'Courses and certifications, newest first.',
    ],

    'notes' => [
        'label' => 'Notes',
        'title' => 'Written down while building.',
        'all' => 'All notes',
        'minutes' => ':n min',
        'lead' => 'Short pieces about problems I ran into, and how I solved them.',
        'empty' => 'Nothing published yet.',
        'back' => 'All notes',
    ],

    'contact' => [
        'label' => 'Contact',
        'title' => 'Write to me.',
        'lead' => 'About a project, Jebbari or anything else. I usually reply within a few days.',
        'or' => 'or use the form',
        'name' => 'Name',
        'email' => 'Email',
        'message' => 'Message',
        'send' => 'Send message',
        'success' => 'Message received. You’ll get a confirmation by email.',
        'error' => 'Sending didn’t work. Feel free to email me directly at :email.',
        'booking' => 'Book a call',
    ],

    'placeholder' => 'Placeholder',
];
