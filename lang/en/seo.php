<?php

// Titles (≤ 60 characters) and meta descriptions (≤ 155) per page. Tested in SeoTest.
return [
    'pages' => [
        'home' => [
            'title' => 'Amir Jebbari – founder of Jebbari, Amsterdam and Zwolle',
            'description' => 'Amir Jebbari is the founder of Jebbari, based in Amsterdam and Zwolle. See his projects, such as ServerScope, and ask him a question.',
        ],
        'work' => [
            'title' => 'Projects and work – Amir Jebbari',
            'description' => 'Web applications and websites built by Amir Jebbari, including ServerScope. For each project: the problem, the approach, the stack and the result.',
        ],
        'certificates' => [
            'title' => 'Certificates – Amir Jebbari',
            'description' => 'Courses and certifications completed by Amir Jebbari, founder of Jebbari. Newest first.',
        ],
        'ask' => [
            'title' => 'Ask Amir – ask Amir Jebbari a question',
            'description' => 'Ask Amir Jebbari a question about web applications, Laravel or running Jebbari. Answered questions appear on this page.',
        ],
        'roadmap' => [
            'title' => 'Roadmap and ideas – amirs.life',
            'description' => 'Visitor ideas for amirs.life and what Amir Jebbari does with them: planned, in progress or shipped. Vote, or suggest something yourself.',
        ],
        'notes' => [
            'title' => 'Notes on building – Amir Jebbari',
            'description' => 'Short pieces by Amir Jebbari about problems he ran into while building web applications with Laravel, and how he solved them.',
        ],
        'cv' => [
            'title' => 'CV – Amir Jebbari, founder of Jebbari',
            'description' => 'CV of Amir Jebbari, founder of Jebbari in Amsterdam and Zwolle: stack, projects and contact details. Also available as a PDF.',
        ],
        'status' => [
            'title' => 'Site status – amirs.life',
            'description' => 'Uptime of Amir Jebbari’s live projects over the last 30 days, measured every 10 minutes.',
        ],
        'privacy' => [
            'title' => 'Privacy – amirs.life',
            'description' => 'What amirs.life stores, why and for how long: visit statistics without trackers, the contact form, questions and ideas.',
        ],
        'not_found' => [
            'title' => 'Page not found – amirs.life',
            'description' => 'This page doesn’t exist. Go back to the home page of Amir Jebbari, founder of Jebbari.',
        ],
        'login' => [
            'title' => 'Log in – amirs.life',
            'description' => 'Log in to manage the site.',
        ],
        'project' => [
            'title' => ':title – project by Amir Jebbari',
            'title_short' => ':title – Amir Jebbari',
            'description' => ':title, built by Amir Jebbari. :line',
        ],
        'note' => [
            'title' => ':title – Amir Jebbari',
            'description' => ':title – a note by Amir Jebbari, founder of Jebbari.',
        ],
    ],

    'breadcrumbs' => [
        'home' => 'Home',
        'work' => 'Work',
        'notes' => 'Notes',
        'certificates' => 'Certificates',
        'ask' => 'Ask Amir',
        'roadmap' => 'Roadmap',
        'cv' => 'CV',
        'status' => 'Status',
        'privacy' => 'Privacy',
    ],

    'og' => [
        'kicker' => [
            'home' => 'Founder of Jebbari',
            'work' => 'Work',
            'certificates' => 'Certificates',
            'ask' => 'Ask Amir',
            'roadmap' => 'Roadmap',
            'notes' => 'Notes',
            'cv' => 'CV',
            'status' => 'Status',
            'privacy' => 'Privacy',
        ],
        'image_alt' => ':title – share image of amirs.life',
    ],

    'jebbari' => [
        'built_with' => 'This project was built with :jebbari.',
        'see_on' => 'See the project on :jebbari',
    ],
];
