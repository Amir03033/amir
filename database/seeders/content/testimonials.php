<?php

/*
 * Pilot reviews: TEXT ONLY, no photos (Amir adds those), seeded HIDDEN (is_visible = false).
 *
 * These are NOT real quotes. Each one is a suggested text to send to a real client, who approves it
 * or rewrites it in their own words. Name, role and company are [VERIFY] placeholders on purpose:
 * no invented people or companies. A review with any [VERIFY] marker cannot be made visible
 * (model guard + Filament rule), so nothing here reaches the site unchanged.
 *
 * 'project' is a project slug (null = a general Jebbari client).
 */

$approve = [
    'nl' => '[VERIFY: voorbeeldtekst – laat de klant deze goedkeuren of in eigen woorden herschrijven]',
    'en' => '[VERIFY: sample text – have the client approve it or rewrite it in their own words]',
];

return [
    [
        'project' => 'serverscope',
        'sort_order' => 1,
        'name' => '[VERIFY: naam van een ServerScope-gebruiker]',
        'role' => '[VERIFY: functie, bijv. ontwikkelaar]',
        'company' => '[VERIFY: bedrijf – alleen met toestemming, anders leeg laten]',
        'quote' => [
            'nl' => 'Sinds we ServerScope gebruiken, weten we het eerder dan onze klanten als er iets misgaat. Een server koppelen was een paar minuten werk, en de meldingen blijven rustig: alleen als er echt iets aan de hand is. '.$approve['nl'],
            'en' => 'Since we started using ServerScope, we know before our customers do when something goes wrong. Connecting a server took a few minutes, and the alerts stay calm: only when something is really wrong. '.$approve['en'],
        ],
    ],
    [
        'project' => 'barbercuts',
        'sort_order' => 2,
        'name' => '[VERIFY: naam van een kapper die BarberCuts gebruikt]',
        'role' => '[VERIFY: eigenaar van de zaak?]',
        'company' => '[VERIFY: naam van de zaak – alleen met toestemming]',
        'quote' => [
            'nl' => "Klanten boeken nu zelf via onze pagina, ook 's avonds. Ik hoef tussen het knippen door de telefoon niet meer op te nemen, en ik zie in één scherm wie er vandaag komt. ".$approve['nl'],
            'en' => 'Customers now book themselves on our page, in the evening too. I no longer have to answer the phone between haircuts, and I see on one screen who is coming in today. '.$approve['en'],
        ],
    ],
    [
        'project' => 'irooni',
        'sort_order' => 3,
        'name' => '[VERIFY: naam – alleen als Irooni voor iemand anders is gebouwd; is het je eigen project, verwijder deze review dan]',
        'role' => '[VERIFY: functie, bijv. hoofdredacteur]',
        'company' => 'Irooni',
        'quote' => [
            'nl' => 'Amir dacht mee over meer dan de techniek: drie talen, Perzisch dat van rechts naar links leest, en een controle die geen artikel zonder bronnen doorlaat. Het magazine ziet eruit zoals ik het voor ogen had. '.$approve['nl'],
            'en' => 'Amir thought along about more than the technology: three languages, Persian that reads right to left, and a check that lets no article through without sources. The magazine looks the way I had pictured it. '.$approve['en'],
        ],
    ],
    [
        'project' => null,
        'sort_order' => 4,
        'name' => '[VERIFY: naam van een Jebbari-klant]',
        'role' => '[VERIFY: functie]',
        'company' => '[VERIFY: bedrijf – alleen met toestemming]',
        'quote' => [
            'nl' => 'Duidelijke afspraken, snel antwoord en een site die precies doet wat we nodig hadden. We spraken steeds direct met Amir, van het eerste gesprek tot de oplevering. '.$approve['nl'],
            'en' => 'Clear agreements, quick answers and a site that does exactly what we needed. We always spoke to Amir directly, from the first conversation to delivery. '.$approve['en'],
        ],
    ],
];
