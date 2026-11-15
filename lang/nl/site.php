<?php

// Copy for the redesigned site. Lines marked DRAFT are written by Claude in Amir's voice and wait for his approval.
// Facts that are missing are not invented: they are placeholders (see the <x-ph> component and DEPLOY.md).

return [
    'meta' => [
        'title' => 'Amir Jebbari — oprichter van Jebbari, bouwer van ServerScope',
        'description' => 'De persoonlijke site van Amir Jebbari: oprichter van webbureau Jebbari in Zwolle en Amsterdam, bouwer van ServerScope en student. Werk, notities en een plek om terug te praten.',
    ],

    'hero' => [
        // DRAFT
        'lines' => ['Ik bouw websites,', 'en soms de tools', 'die ze bewaken.'],
        'role' => 'Oprichter van Jebbari · Zwolle & Amsterdam',
        'time' => 'Amsterdam',
        'stamp' => 'Zet een stempel',
    ],

    'work' => [
        'screenshot_alt' => 'Screenshot van :name',
        'label' => 'Werk',
        'title' => 'Dingen die ik bouwde.', // DRAFT
        'case' => 'Bekijk de case',
        'live' => 'Live site',
        'more' => 'Meer werk',
        'all' => 'Alle projecten',
        'empty' => 'Hier komen projecten zodra ze live staan.',
        'no_image' => 'Screenshot volgt',
        'role' => 'Rol',
        'year' => 'Jaar',
        'stack' => 'Stack',
        'link' => 'Link',
        'index_title' => 'Werk',
        'index_lead' => 'Alles wat ik bouwde en dat live staat, met per project wat ik deed en waarmee.',
        'filter_all' => 'Alles',
        'filter_label' => 'Filter op stack',
    ],

    'case' => [
        'crumb' => 'Werk',
        'problem' => 'Het probleem',
        'solution' => 'Wat ik bouwde',
        'result' => 'Het resultaat',
        'about' => 'Over het project',
        'gallery' => 'Screenshots',
        'visit' => 'Bekijk de live site',
        'source' => 'Broncode',
        'uptime' => 'Uptime, laatste 30 dagen',
        'uptime_note' => 'Elke 10 minuten gemeten door deze site.',
        'since' => 'gemeten sinds :date',
        'previous' => 'Vorige',
        'next' => 'Volgende',
        'missing_case' => 'probleem, aanpak en resultaat',
        'missing_meta' => 'jaar en rol',
        'image_n' => ':name, screenshot :n',
    ],

    'about' => [
        'label' => 'Over mij',
        'title' => 'Student, oprichter, bouwer.', // DRAFT
        'photo_alt' => 'Amir Jebbari op een brug in Salzburg',
        // DRAFT, built from facts already on the old site and in the brief.
        'text' => 'Ik ben Amir. Ik run Jebbari, een webbureau in Zwolle en Amsterdam, en daarnaast studeer ik.',
        'text_2' => 'Naast klantwerk bouw ik eigen producten. ServerScope is de grootste: monitoring voor servers en domeinen. Het leerde me wat erbij komt kijken om software te draaien in plaats van alleen te schrijven.',
        'facts' => [
            'agency' => 'Bureau',
            'cities' => 'Steden',
            'product' => 'Eigen product',
            'study' => 'Studie',
            'since' => 'Jebbari sinds',
        ],
        'cities_value' => 'Zwolle & Amsterdam',
        'to_jebbari' => 'Naar Jebbari',
        'cv' => 'Cv bekijken',
    ],

    'testimonials' => [
        'label' => 'Wat anderen zeggen',
    ],

    'certificates' => [
        'label' => 'Certificaten',
        'title' => 'Wat ik afrondde.',
        'all' => 'Alle certificaten',
        'view' => 'Bekijk',
        'empty' => 'Nog geen certificaten toegevoegd.',
        'lead' => 'Cursussen en certificeringen, de nieuwste eerst.',
    ],

    'notes' => [
        'label' => 'Notities',
        'title' => 'Opgeschreven tijdens het bouwen.',
        'all' => 'Alle notities',
        'minutes' => ':n min',
        'lead' => 'Korte stukken over problemen die ik tegenkwam, en hoe ik ze oploste.',
        'empty' => 'Nog niets gepubliceerd.',
        'back' => 'Alle notities',
    ],

    'contact' => [
        'label' => 'Contact',
        'title' => 'Schrijf me.',
        'lead' => 'Over een project, Jebbari of iets anders. Ik reageer meestal binnen een paar dagen.',
        'or' => 'of gebruik het formulier',
        'name' => 'Naam',
        'email' => 'E-mailadres',
        'message' => 'Bericht',
        'send' => 'Verstuur bericht',
        'success' => 'Bericht ontvangen. Je krijgt een bevestiging per mail.',
        'error' => 'Versturen lukte niet. Mail me gerust direct op :email.',
        'booking' => 'Plan een gesprek',
    ],

    'placeholder' => 'Placeholder',
];
