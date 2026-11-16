<?php

/*
|--------------------------------------------------------------------------
| Case studies, seeded as UNPUBLISHED drafts (CaseStudiesSeeder).
|--------------------------------------------------------------------------
| Sources: the repos Amir03033/ServerScope (README, agent/README, docs/AUDIT.md, docs/CHANGELOG),
| Amir03033/ServerScope-Info (content/docs), Amir03033/barbercuts (README, docs/BOUWOPDRACHT.md §15)
| and Amir03033/jebbari, plus the live public sites (checked 2026-11-16).
| Anything not confirmed by those sources carries a visible [VERIFY: …] marker and is listed in
| REVIEW.md. A project can't be published until every marker is gone.
| Barbercuts.nl is its own project and repo; nothing here comes from JF Cuts.
*/

return [
    // ------------------------------------------------------------------------------------------
    'serverscope' => [
        'sort_order' => 1,
        'featured' => true,
        'year' => 2026, // REVIEW.md: confirm the year (not guarded: numeric field)
        'status' => 'live',
        'tags' => 'Laravel, Livewire, Python, Bash, MySQL, Tailwind CSS, Chart.js, Web Push',
        'demo_url' => 'https://serverscope.nl',
        'jebbari_url' => [
            'nl' => 'https://jebbari.nl/nl/werk/serverscope',
            'en' => 'https://jebbari.nl/en/work/serverscope',
        ],
        'title' => ['nl' => 'ServerScope', 'en' => 'ServerScope'],
        'summary' => [
            'nl' => 'Monitoring voor servers, websites, domeinen en geplande taken: een agent die alleen leest, en een melding voordat klanten iets merken.',
            'en' => 'Monitoring for servers, websites, domains and scheduled jobs: an agent that only reads, and an alert before customers notice.',
        ],
        'role' => [
            'nl' => 'Ontwerp en bouw, van begin tot eind',
            'en' => 'Design and build, end to end',
        ],
        'description' => [
            'nl' => 'ServerScope is een monitoringplatform voor servers, websites, domeinen en geplande taken. Een lichte agent stuurt servermetingen, websites en domeinen worden van buitenaf gecontroleerd, en problemen worden incidenten met een tijdlijn en een oorzaakanalyse.',
            'en' => 'ServerScope is a monitoring platform for servers, websites, domains and scheduled jobs. A lightweight agent sends server metrics, websites and domains are checked from the outside, and problems become incidents with a timeline and a root cause analysis.',
        ],
        'problem' => [
            'nl' => "Wie een paar servers en websites draait, hoort vaak pas van een storing als een klant belt. Een certificaat verloopt, een schijf loopt vol of een nachtelijke back-up draait niet, en niemand ziet het. De grote monitoringtools zijn voor een klein team vaak te zwaar of te duur. [VERIFY: is dit de aanleiding zoals jij hem zou vertellen, en voor wie bouwde je het in eerste instantie?]\n\nIk wilde één plek waar servers, websites, domeinen en geplande taken samenkomen, met meldingen die op tijd komen en niet te vaak.",
            'en' => "If you run a few servers and websites, you often only hear about an outage when a customer calls. A certificate expires, a disk fills up or a nightly backup doesn't run, and nobody notices. The big monitoring tools are often too heavy or too expensive for a small team. [VERIFY: is this the reason as you would tell it, and who did you build it for first?]\n\nI wanted one place where servers, websites, domains and scheduled jobs come together, with alerts that arrive in time and not too often.",
        ],
        'solution' => [
            'nl' => "- **Een agent die alleen leest.** Een klein Python-programma meet elke 60 seconden CPU, geheugen, schijven, netwerk en diensten en stuurt dat via HTTPS naar ServerScope. Het voert geen opdrachten uit en heeft geen open poort nodig, alleen uitgaand verkeer. Zo is er weinig dat misbruikt kan worden. Voor machines zonder Python is er een Bash-versie.\n- **Een server koppelen met één commando.** Het installatiescript vraagt om een eenmalige koppeltoken die een uur geldig is. Die staat bewust niet in het commando, zodat hij niet in de shellgeschiedenis belandt.\n- **Ondertekende updates.** De agent installeert alleen een ondertekende release en stopt als de sleutel van een server ineens anders is. Zo kan een gekaapte download geen server overnemen.\n- **Incidenten in plaats van losse meldingen.** Een mislukte controle opent een incident met een tijdlijn en een oorzaakanalyse, dat vanzelf sluit als het weer werkt. Meldingen gaan per e-mail, push, Slack, Teams, Telegram of Discord.\n- **Teams, rollen en 2FA vanaf het begin.** Meerdere mensen per team, met tweestapsverificatie, statuspagina's en een REST-API met webhooks.",
            'en' => "- **An agent that only reads.** A small Python program measures CPU, memory, disks, network and services every 60 seconds and sends it to ServerScope over HTTPS. It runs no commands and needs no open port, only outbound traffic, so there is little to abuse. For machines without Python there is a Bash version.\n- **Connecting a server with one command.** The install script asks for a one-time enrollment token that is valid for an hour. It is deliberately not part of the command, so it never ends up in the shell history.\n- **Signed updates.** The agent only installs a signed release and stops when a server's key suddenly changes, so a hijacked download can't take over a server.\n- **Incidents instead of loose alerts.** A failed check opens an incident with a timeline and a root cause analysis, which closes by itself when things work again. Alerts go out by email, push, Slack, Teams, Telegram or Discord.\n- **Teams, roles and 2FA from the start.** Several people per team, with two-factor authentication, status pages and a REST API with webhooks.",
        ],
        'lessons' => [
            'nl' => "- **Test op dezelfde database als productie.** De tests draaiden lange tijd op SQLite. Het auditdocument noemt vier oudere tests die op MySQL 8 niet werkten; dat zag je lokaal dus niet. In latere projecten draai ik de tests daarom op MySQL.\n- **De installatie is het spannendste stuk.** Een script dat als root draait moet je kunnen vertrouwen. Het meeste werk zat niet in het verzamelen van metingen, maar in koppeltokens, ondertekende releases en het weigeren van alles wat niet klopt.\n- **Schrijf op wat er af is.** ServerScope groeide snel: volgens het auditdocument van 61 tests bij de eerste audit naar ruim 680 tests een paar fases later. Een audit met per onderdeel \"compleet / gedeeltelijk / ontbreekt\" hield het overzicht. [VERIFY: klopt deze les zo voor jou?]",
            'en' => "- **Test on the same database as production.** For a long time the tests ran on SQLite. The audit document lists four older tests that didn't work on MySQL 8, so you didn't see that locally. In later projects I run the tests on MySQL.\n- **Installation is the hardest part.** A script that runs as root has to be trustworthy. Most of the work wasn't in collecting metrics but in enrollment tokens, signed releases and refusing anything that doesn't add up.\n- **Write down what's finished.** ServerScope grew fast: according to the audit document from 61 tests at the first audit to more than 680 tests a few phases later. An audit with \"complete / partial / missing\" per feature kept the overview. [VERIFY: is this lesson right for you as written?]",
        ],
        'result' => [
            'nl' => "ServerScope staat live op serverscope.nl, met een openbare demo met voorbeeldgegevens en documentatie in het Nederlands en Engels op info.serverscope.nl. Er zijn drie abonnementen: Free, Pro en Premium. [VERIFY: wil je hier een concreet resultaat noemen, zoals het aantal teams of servers? Alleen als je het kunt onderbouwen.]",
            'en' => "ServerScope is live at serverscope.nl, with a public demo using sample data and documentation in Dutch and English at info.serverscope.nl. There are three plans: Free, Pro and Premium. [VERIFY: do you want to mention a concrete result here, such as the number of teams or servers? Only if you can back it up.]",
        ],
        'seo_title' => [
            'nl' => 'ServerScope – monitoring, gebouwd door Amir Jebbari',
            'en' => 'ServerScope – monitoring built by Amir Jebbari',
        ],
        'seo_description' => [
            'nl' => 'Hoe ik ServerScope bouwde: een agent die alleen leest, koppelen met één commando, ondertekende updates en incidenten met een tijdlijn.',
            'en' => 'How I built ServerScope: an agent that only reads, one-command enrollment, signed updates and incidents with a timeline.',
        ],
        'image' => 'demo-dashboard-desktop-light.webp',
        'image_alt' => [
            'nl' => 'Dashboard van ServerScope in de demo met voorbeeldgegevens: gezondheidsscore, servers en incidenten',
            'en' => 'ServerScope dashboard in the demo with sample data: health score, servers and incidents',
        ],
        'gallery' => [
            ['demo-dashboard-desktop-dark.webp', 'Hetzelfde dashboard in het donkere thema', 'The same dashboard in the dark theme'],
            ['demo-incidents-desktop-light.webp', 'Incidentenoverzicht met MTTA, MTTR en de incidentfrequentie van 30 dagen (voorbeeldgegevens)', 'Incident overview with MTTA, MTTR and 30-day incident frequency (sample data)'],
            ['demo-dashboard-mobile-light.webp', 'Het dashboard op een telefoon, met de tabbalk onderin', 'The dashboard on a phone, with the tab bar at the bottom'],
            ['demo-dashboard-mobile-dark.webp', 'Het dashboard op een telefoon in het donkere thema', 'The dashboard on a phone in the dark theme'],
            ['site-home-desktop-light.webp', 'De productsite info.serverscope.nl met het installatiecommando', 'The product site info.serverscope.nl with the install command'],
            ['site-home-mobile-dark.webp', 'De productsite op een telefoon, donker thema', 'The product site on a phone, dark theme'],
            ['site-pricing-desktop-light.webp', 'De prijzenpagina met de abonnementen Free, Pro en Premium', 'The pricing page with the Free, Pro and Premium plans'],
        ],
    ],

    // ------------------------------------------------------------------------------------------
    'barbercuts' => [
        'sort_order' => 2,
        'featured' => true,
        'year' => 2026, // REVIEW.md: confirm the year
        'status' => 'live',
        'tags' => 'Laravel, Livewire, Filament, Tailwind CSS, MySQL, Redis, Mollie, PWA',
        'demo_url' => 'https://barbercuts.nl',
        'jebbari_url' => [
            'nl' => 'https://jebbari.nl/nl/werk/barbercuts-slim-afspraken-en-salonbeheer',
            'en' => 'https://jebbari.nl/en/work/barbercuts-smart-appointment-barbershop-management',
        ],
        'title' => ['nl' => 'BarberCuts', 'en' => 'BarberCuts'],
        'summary' => [
            'nl' => 'Afspraken, agenda en omzet voor kapperszaken, in één app op de telefoon. Klanten boeken zelf via de pagina van de zaak.',
            'en' => 'Appointments, calendar and revenue for barbershops, in one app on the phone. Customers book themselves on the shop’s own page.',
        ],
        'role' => [
            'nl' => 'Ontwerp en bouw [VERIFY: alleen bouw, of ook eigenaar van het product?]',
            'en' => 'Design and build [VERIFY: build only, or also product owner?]',
        ],
        'description' => [
            'nl' => 'BarberCuts is een platform voor kapperszaken in Nederland. Een zaak meldt zich zelf aan en beheert afspraken, een agenda per kapper, klanten en omzet in één app. Klanten boeken via de eigen pagina van de zaak, met of zonder account.',
            'en' => 'BarberCuts is a platform for barbershops in the Netherlands. A shop signs itself up and manages appointments, a calendar per barber, customers and revenue in one app. Customers book through the shop’s own page, with or without an account.',
        ],
        'problem' => [
            'nl' => "Veel kappers plannen afspraken nog via de telefoon, WhatsApp of een papieren agenda, terwijl ze met een schaar in hun hand staan. Klanten willen 's avonds op hun telefoon een tijd kiezen zonder te bellen. [VERIFY: is dit de aanleiding, en hoorde je dit van een kapper die je kent?]\n\nDe opdracht was een platform dat eerst op de telefoon werkt: een zaak moet zich binnen drie minuten kunnen aanmelden, en een klant moet in een halve minuut kunnen boeken.",
            'en' => "Many barbers still plan appointments by phone, WhatsApp or a paper calendar, while holding a pair of scissors. Customers want to pick a time on their phone in the evening without calling. [VERIFY: is this the reason, and did you hear it from a barber you know?]\n\nThe brief was a platform that works on the phone first: a shop must be able to sign up within three minutes, and a customer must be able to book in half a minute.",
        ],
        'solution' => [
            'nl' => "- **Eerst de telefoon.** Elk scherm is eerst op 375 pixels breed gebouwd. Playwright test de belangrijkste stappen op twee telefoonformaten en maakt er schermafbeeldingen van.\n- **Elke zaak apart, en dicht bij twijfel.** Een zaak ziet nooit gegevens van een andere zaak. Weet de app in een verzoek niet welke zaak het is, dan krijg je niets te zien in plaats van alles. Een kapper ziet alleen zijn eigen omzet; de eigenaar ziet het totaal.\n- **Boeken zonder account.** Naam en telefoonnummer zijn genoeg. De klant krijgt een eigen link om de afspraak te bekijken, te verplaatsen of te annuleren, en een agenda-bestand.\n- **Betalen achter een eigen laag.** Mollie zit achter een eigen interface, zodat tests een nagebootste provider gebruiken. Betalen staat tijdens de lancering uit; aanbetalingen zijn voorbereid maar nog niet actief.\n- **Een app zonder app store.** BarberCuts is een PWA: je zet hem op je beginscherm en kappers krijgen meldingen bij nieuwe boekingen.",
            'en' => "- **The phone first.** Every screen was built at 375 pixels wide first. Playwright tests the main steps on two phone sizes and takes screenshots of them.\n- **Every shop separate, and closed when in doubt.** A shop never sees another shop's data. If the app doesn't know which shop a request belongs to, you see nothing instead of everything. A barber sees only their own revenue; the owner sees the total.\n- **Booking without an account.** A name and phone number are enough. The customer gets a personal link to view, move or cancel the appointment, plus a calendar file.\n- **Payments behind their own layer.** Mollie sits behind its own interface, so tests use a fake provider. Payments are off during the launch; deposits are prepared but not active yet.\n- **An app without an app store.** BarberCuts is a PWA: you add it to your home screen and barbers get notifications for new bookings.",
        ],
        'lessons' => [
            'nl' => "- **\"Dicht bij twijfel\" moet je testen, niet aannemen.** Dat een zaak niets van een andere zaak ziet, staat per fase in tests. Ook gemanipuleerde verzoeken met het id van een andere zaak geven een 404.\n- **Deploy-details bijten.** Op de server stond de repository eerst als `--bare` clone; dan haalt `git fetch` geen nieuwe commits op in de lokale branches. Met `--mirror` werkte het wel. Klein ding, een middag kwijt. [VERIFY: klopt \"een middag\"?]\n- **Schrijf keuzes op met een reden.** Elke keuze die niet in de opdracht stond, staat met datum en reden in het projectdocument. Dat scheelde bij elke volgende fase uitleg.",
            'en' => "- **\"Closed when in doubt\" has to be tested, not assumed.** That a shop sees nothing of another shop is covered by tests in every phase. Even manipulated requests with another shop's id return a 404.\n- **Deployment details bite.** On the server the repository was first a `--bare` clone, and then `git fetch` doesn't bring new commits into the local branches. With `--mirror` it worked. A small thing, an afternoon gone. [VERIFY: is \"an afternoon\" right?]\n- **Write decisions down with a reason.** Every decision that wasn't in the brief is in the project document with a date and a reason. That saved explaining at every next phase.",
        ],
        'result' => [
            'nl' => "BarberCuts staat live op barbercuts.nl en is tijdens de lancering gratis; daarna kost het € 10 per maand, zonder kosten per kapper of per afspraak. [VERIFY: aantal aangemelde zaken of afspraken, alleen als je het wilt en kunt onderbouwen.]",
            'en' => "BarberCuts is live at barbercuts.nl and free during the launch; after that it costs €10 a month, with no fees per barber or per appointment. [VERIFY: number of shops or bookings, only if you want to share it and can back it up.]",
        ],
        'seo_title' => [
            'nl' => 'BarberCuts – boekingsapp voor kappers, door Amir Jebbari',
            'en' => 'BarberCuts – booking app for barbers, by Amir Jebbari',
        ],
        'seo_description' => [
            'nl' => 'Hoe ik BarberCuts bouwde: eerst voor de telefoon, elke kapperszaak strikt gescheiden, boeken zonder account en Mollie achter een eigen laag.',
            'en' => 'How I built BarberCuts: phone first, every barbershop strictly separated, booking without an account and Mollie behind its own layer.',
        ],
        'image' => 'home-desktop-light.webp',
        'image_alt' => [
            'nl' => 'De homepage van barbercuts.nl met een telefoon die de app toont',
            'en' => 'The barbercuts.nl home page with a phone showing the app',
        ],
        'gallery' => [
            ['home-mobile-light.webp', 'De homepage van BarberCuts op een telefoon', 'The BarberCuts home page on a phone'],
            ['pricing-desktop-light.webp', 'De prijzenpagina: één abonnement van € 10 per maand, gratis tijdens de lancering', 'The pricing page: one plan of €10 a month, free during the launch'],
            ['pricing-mobile-light.webp', 'De prijzenpagina op een telefoon', 'The pricing page on a phone'],
            ['signup-mobile-light.webp', 'Stap 1 van 6 bij het aanmelden van een zaak, op een telefoon (leeg formulier)', 'Step 1 of 6 of signing up a shop, on a phone (empty form)'],
        ],
    ],

    // ------------------------------------------------------------------------------------------
    'jebbari' => [
        'sort_order' => 3,
        'featured' => true,
        'year' => 2026, // REVIEW.md: confirm the year
        'status' => 'live',
        'tags' => 'Laravel, Filament, Blade, Alpine.js, Tailwind CSS, Spatie Media Library',
        'demo_url' => 'https://jebbari.nl',
        'jebbari_url' => null,
        'title' => ['nl' => 'Jebbari.nl', 'en' => 'Jebbari.nl'],
        'summary' => [
            'nl' => 'De site van mijn bureau: diensten, werk, een blog en een projectaanvraag in vijf stappen, in het Nederlands en Engels.',
            'en' => 'The site of my agency: services, work, a blog and a five-step project request, in Dutch and English.',
        ],
        'role' => [
            'nl' => 'Ontwerp, bouw en teksten',
            'en' => 'Design, build and copy',
        ],
        'description' => [
            'nl' => 'Jebbari.nl is de site van Jebbari, mijn bureau in Amsterdam en Zwolle. Hij laat zien wat ik bouw en maakt het makkelijk om een project aan te vragen, zonder eerst te hoeven bellen.',
            'en' => 'Jebbari.nl is the site of Jebbari, my agency in Amsterdam and Zwolle. It shows what I build and makes it easy to request a project without having to call first.',
        ],
        'problem' => [
            'nl' => "Een bureau zonder eigen site bestaat voor veel mensen niet. Ik wilde een site die in een paar seconden duidelijk maakt wat Jebbari doet, en waar iemand met alleen een ruw idee toch een aanvraag kan doen. [VERIFY: is dit hoe jij de aanleiding zou omschrijven?]\n\nOmdat ik voor klanten in het Nederlands en Engels werk, moest alles in beide talen, met eigen adressen per taal.",
            'en' => "To many people, an agency without its own site doesn't exist. I wanted a site that makes clear in a few seconds what Jebbari does, and where someone with only a rough idea can still send a request. [VERIFY: is this how you would describe the reason?]\n\nBecause I work for clients in Dutch and English, everything had to be in both languages, with its own addresses per language.",
        ],
        'solution' => [
            'nl' => "- **Een aanvraag in vijf korte stappen.** Wat, doel, budget, functies en contact, één vraag per scherm. Antwoorden worden per stap bewaard, zodat niemand opnieuw hoeft te beginnen.\n- **Twee talen met eigen adressen.** `/nl/diensten` en `/en/services`, met de juiste taalverwijzingen, zodat zoekmachines beide versies goed vinden.\n- **Beheer in Filament.** Projecten, diensten en blogposts beheer ik zelf, met een mediabibliotheek voor afbeeldingen en een logboek van wijzigingen.\n- **Een gratis website-check.** Bezoekers vullen een adres in en krijgen een rapport over beveiliging, SEO, snelheid en toegankelijkheid, in gewone taal. [VERIFY: wil je noemen hoe vaak de check gebruikt wordt?]\n- **Vindbaar voor zoekmachines en AI.** Een sitemap, gestructureerde gegevens en een llms.txt die in één zin zegt wie ik ben, dezelfde zin als op deze site.",
            'en' => "- **A request in five short steps.** What, goal, budget, features and contact, one question per screen. Answers are saved per step, so nobody has to start over.\n- **Two languages with their own addresses.** `/nl/diensten` and `/en/services`, with the right language links, so search engines find both versions.\n- **Managed in Filament.** I manage projects, services and blog posts myself, with a media library for images and a log of changes.\n- **A free website check.** Visitors enter an address and get a report on security, SEO, speed and accessibility, in plain language. [VERIFY: do you want to mention how often the check is used?]\n- **Findable for search engines and AI.** A sitemap, structured data and an llms.txt that says in one sentence who I am, the same sentence as on this site.",
        ],
        'lessons' => [
            'nl' => "- **Een redirect kan je metingen opeten.** `/nl/aanvraag` stuurt door naar stap 1 en laat daarbij de UTM-parameters vallen. Links van buitenaf wijzen daarom rechtstreeks naar stap 1.\n- **Twee sites, één verhaal.** Naam, plaatsen en de zin die zegt wie ik ben moeten op jebbari.nl en amirs.life precies hetzelfde zijn. Kleine verschillen, zoals \"digital studio\" tegenover \"webbureau\", vallen pas op als je ze naast elkaar legt. [VERIFY: welke term kies je?]\n- **Eerst de inhoud, dan het ontwerp.** [VERIFY: klopt dit voor hoe jij Jebbari.nl bouwde? Pas aan of schrap.]",
            'en' => "- **A redirect can eat your measurements.** `/en/request` redirects to step 1 and drops the UTM parameters along the way. Links from outside therefore point straight to step 1.\n- **Two sites, one story.** Name, cities and the sentence that says who I am must be exactly the same on jebbari.nl and amirs.life. Small differences, such as \"digital studio\" versus \"web agency\", only show when you put them side by side. [VERIFY: which term do you choose?]\n- **Content first, then design.** [VERIFY: is this true for how you built Jebbari.nl? Adjust or remove.]",
        ],
        'result' => [
            'nl' => "Jebbari.nl staat live in het Nederlands en Engels, met zeven diensten, werk, een blog, de website-check en de aanvraag in vijf stappen. [VERIFY: resultaat, zoals het aantal aanvragen per maand, alleen als je het wilt delen.]",
            'en' => "Jebbari.nl is live in Dutch and English, with seven services, work, a blog, the website check and the five-step request. [VERIFY: a result, such as requests per month, only if you want to share it.]",
        ],
        'seo_title' => [
            'nl' => 'Jebbari.nl – de site van mijn bureau, door Amir Jebbari',
            'en' => 'Jebbari.nl – my agency’s site, by Amir Jebbari',
        ],
        'seo_description' => [
            'nl' => 'Hoe ik jebbari.nl bouwde: een projectaanvraag in vijf stappen, twee talen met eigen adressen, beheer in Filament en een gratis website-check.',
            'en' => 'How I built jebbari.nl: a five-step project request, two languages with their own addresses, Filament for content and a free website check.',
        ],
        'image' => 'home-desktop-light.webp',
        'image_alt' => [
            'nl' => 'De homepage van jebbari.nl: "Digitale producten bouwen. Maken wat hierna komt."',
            'en' => 'The jebbari.nl home page: "Building digital products. Making what comes next."',
        ],
        'gallery' => [
            ['home-desktop-dark.webp', 'Dezelfde homepage in het donkere thema', 'The same home page in the dark theme'],
            ['services-desktop-light.webp', 'De dienstenpagina: wat ik voor je kan bouwen', 'The services page: what I can build for you'],
            ['request-desktop-light.webp', 'Stap 1 van 5 van de projectaanvraag (leeg formulier)', 'Step 1 of 5 of the project request (empty form)'],
            ['home-mobile-light.webp', 'De homepage op een telefoon', 'The home page on a phone'],
            ['home-mobile-dark.webp', 'De homepage op een telefoon in het donkere thema', 'The home page on a phone in the dark theme'],
            ['request-mobile-light.webp', 'De projectaanvraag op een telefoon (leeg formulier)', 'The project request on a phone (empty form)'],
        ],
    ],
];
