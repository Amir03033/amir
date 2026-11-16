<?php

// Titles (≤ 60 characters) and meta descriptions (≤ 155) per page. Tested in SeoTest.
return [
    'pages' => [
        'home' => [
            'title' => 'Amir Jebbari – oprichter van Jebbari, Amsterdam en Zwolle',
            'description' => 'Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle. Bekijk zijn projecten, zoals ServerScope, en stel hem een vraag.',
        ],
        'work' => [
            'title' => 'Projecten en werk – Amir Jebbari',
            'description' => 'Webapplicaties en websites die Amir Jebbari bouwde, waaronder ServerScope. Per project: het probleem, de aanpak, de stack en het resultaat.',
        ],
        'certificates' => [
            'title' => 'Certificaten – Amir Jebbari',
            'description' => 'Cursussen en certificeringen die Amir Jebbari, oprichter van Jebbari, heeft afgerond. De nieuwste staan bovenaan.',
        ],
        'ask' => [
            'title' => 'Vraag het Amir – stel Amir Jebbari een vraag',
            'description' => 'Stel Amir Jebbari een vraag over webapplicaties, Laravel of ondernemen met Jebbari. Beantwoorde vragen verschijnen op deze pagina.',
        ],
        'roadmap' => [
            'title' => 'Roadmap en ideeën – amirs.life',
            'description' => 'Ideeën van bezoekers voor amirs.life en wat Amir Jebbari ermee doet: gepland, mee bezig of gelanceerd. Stem mee of stel zelf iets voor.',
        ],
        'notes' => [
            'title' => 'Notities over bouwen – Amir Jebbari',
            'description' => 'Korte stukken van Amir Jebbari over problemen die hij tegenkwam bij het bouwen van webapplicaties met Laravel, en hoe hij ze oploste.',
        ],
        'cv' => [
            'title' => 'Cv – Amir Jebbari, oprichter van Jebbari',
            'description' => 'Cv van Amir Jebbari, oprichter van Jebbari in Amsterdam en Zwolle: stack, projecten en contact. Ook te downloaden als pdf.',
        ],
        'status' => [
            'title' => 'Status van de sites – amirs.life',
            'description' => 'Uptime van de live projecten van Amir Jebbari over de laatste 30 dagen, elke 10 minuten gemeten.',
        ],
        'privacy' => [
            'title' => 'Privacy – amirs.life',
            'description' => 'Welke gegevens amirs.life opslaat, waarom en hoe lang: bezoekstatistieken zonder trackers, het contactformulier, vragen en ideeën.',
        ],
        'not_found' => [
            'title' => 'Pagina niet gevonden – amirs.life',
            'description' => 'Deze pagina bestaat niet. Ga terug naar de homepage van Amir Jebbari, oprichter van Jebbari.',
        ],
        'login' => [
            'title' => 'Inloggen – amirs.life',
            'description' => 'Inloggen voor beheer.',
        ],
        'project' => [
            'title' => ':title – project van Amir Jebbari',
            'title_short' => ':title – Amir Jebbari',
            'description' => ':title, gebouwd door Amir Jebbari. :line',
        ],
        'note' => [
            'title' => ':title – Amir Jebbari',
            'description' => ':title – een notitie van Amir Jebbari, oprichter van Jebbari.',
        ],
    ],

    'breadcrumbs' => [
        'home' => 'Home',
        'work' => 'Werk',
        'notes' => 'Notities',
        'certificates' => 'Certificaten',
        'ask' => 'Vraag het Amir',
        'roadmap' => 'Roadmap',
        'cv' => 'Cv',
        'status' => 'Status',
        'privacy' => 'Privacy',
    ],

    'og' => [
        'kicker' => [
            'home' => 'Oprichter van Jebbari',
            'work' => 'Werk',
            'certificates' => 'Certificaten',
            'ask' => 'Vraag het Amir',
            'roadmap' => 'Roadmap',
            'notes' => 'Notities',
            'cv' => 'Cv',
            'status' => 'Status',
            'privacy' => 'Privacy',
        ],
        'image_alt' => ':title – deelafbeelding van amirs.life',
    ],

    'jebbari' => [
        'built_with' => 'Dit project is gebouwd met :jebbari.',
        'see_on' => 'Bekijk het project op :jebbari',
    ],
];
