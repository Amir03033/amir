<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Project::query()->where('demo_url', 'https://example.com')->delete();

        Project::updateOrCreate(
            ['demo_url' => 'https://klus-klaar.newdeveloper.nl'],
            [
                'title' => ['nl' => 'KlusKlaar', 'en' => 'KlusKlaar'],
                'description' => [
                    'nl' => "KlusKlaar is een Laravel-site voor een klussendienst: meubels monteren, schilderijen ophangen, kleine reparaties. Bezoekers vragen een klus aan; de eigenaar beheert aanvragen via een dashboard.\n\nIk bouwde authenticatie met Fortify, contactformulieren met e-mailbevestiging, bestandsuploads en een berichtenstroom tussen klant en beheerder. De frontend is eigen CSS en JavaScript, gebundeld met Vite.\n\nLive staat de site op een Docker-omgeving. Dat dwong me om migrations, mail en storage goed te zetten — niet alleen lokaal in SQLite.",
                    'en' => "KlusKlaar is a Laravel site for a handyman service: furniture assembly, hanging art, small repairs. Visitors request a job; the owner manages requests from a dashboard.\n\nI built authentication with Fortify, contact forms with email confirmation, file uploads, and a message thread between customer and admin. The frontend is custom CSS and JavaScript, bundled with Vite.\n\nThe site runs in Docker, which meant getting migrations, mail and storage right — not only locally on SQLite.",
                ],
                'tags' => 'PHP, Laravel, JavaScript, CSS, Vite',
                'github_url' => null,
                'demo_url' => 'https://klus-klaar.newdeveloper.nl',
            ]
        );

        Project::updateOrCreate(
            ['demo_url' => 'https://jungletuintje.newdeveloper.nl'],
            [
                'title' => ['nl' => 'Jungle Tuinen', 'en' => 'Jungle Gardens'],
                'description' => [
                    'nl' => "Jungle Tuinen is een Vue-website over het aanleggen van een jungletuin: stappenplan, foto’s, een kaart met plekken in Zwolle en een contactformulier.\n\nIk zette de site op met Vue en Tailwind, met aparte pagina’s voor informatie, stappen en galerij. De kaart en contentstructuur waren het lastigst: informatie moest scannable blijven zonder een lange landingspagina te worden.\n\nHet project staat live. Daarna schreef ik op deze portfolio hoe ik Vue-routing en content opdeelde.",
                    'en' => "Jungle Gardens is a Vue site about building a jungle garden: a step-by-step plan, photos, a map of places in Zwolle, and a contact form.\n\nI built it with Vue and Tailwind, with separate pages for information, steps and gallery. The map and content structure were the hard part: it had to stay scannable without becoming one long landing page.\n\nThe project is live. I later wrote on this portfolio how I split Vue routing and content.",
                ],
                'tags' => 'Vue, JavaScript, Tailwind, HTML',
                'github_url' => null,
                'demo_url' => 'https://jungletuintje.newdeveloper.nl',
            ]
        );

        Project::updateOrCreate(
            ['demo_url' => 'https://mastermindglow.newdeveloper.nl'],
            [
                'title' => ['nl' => 'MasterMind', 'en' => 'MasterMind'],
                'description' => [
                    'nl' => "MasterMind is een online codekraak-spel: je raadt een geheime code van kleuren en krijgt per poging feedback (goed, verkeerde plek, of niet in de code). Er is een makkelijke en een moeilijkere modus, plus accounts zodat je voortgang bewaart.\n\nIk bouwde de spellogica, login/registratie en de UI rond feedback na elke ronde. Het lastige zat in duidelijke feedback zonder het antwoord te verklappen, en in sessies die blijven werken na refresh.\n\nHet spel staat live (login vereist). Dat maakte auth geen extraatje maar onderdeel van het product.",
                    'en' => "MasterMind is an online code-breaking game: you guess a secret colour code and get feedback each round (correct, wrong place, or not in the code). There is an easy and a harder mode, plus accounts so progress is saved.\n\nI built the game logic, login/registration, and the UI around feedback after each round. The hard part was clear feedback without revealing the answer, and sessions that survive a refresh.\n\nThe game is live (login required), so auth is part of the product rather than an extra.",
                ],
                'tags' => 'PHP, Laravel, JavaScript, CSS',
                'github_url' => null,
                'demo_url' => 'https://mastermindglow.newdeveloper.nl',
            ]
        );

        Blog::updateOrCreate(
            ['slug' => 'mijn-tech-stack-2026'],
            [
                'title' => [
                    'nl' => 'Wat ik écht gebruik: Laravel, Vue en dingen die live staan',
                    'en' => 'What I actually use: Laravel, Vue, and things that ship',
                ],
                'content' => [
                    'nl' => "Ik leer het meest van projecten die iemand anders kan openen. KlusKlaar draait op Laravel: login, mail, uploads. Jungle Tuinen is Vue met een contentstructuur die op mobiel nog te volgen is. MasterMind dwong me om spellogica én accounts samen te laten werken.\n\nHTML en CSS blijven de basis, maar de winst zit in hoe die stack samenhangt. Routing in Laravel, componenten in Vue, Git zodat ik terug kan. Tailwind gebruik ik hier op het portfolio omdat ik het ontwerp zelf in de hand wil houden, gebundeld via Vite — geen CDN op de live site.\n\nVolgende stap is dezelfde zorg voor data: seeders zodat live niet leeg is, en cases per project in plaats van alleen een knop ‘demo’.",
                    'en' => "I learn most from projects someone else can open. KlusKlaar runs on Laravel: login, mail, uploads. Jungle Gardens is Vue with a content structure that still works on a phone. MasterMind forced game logic and accounts to work together.\n\nHTML and CSS stay the foundation, but the value is how the stack fits. Laravel routing, Vue components, Git so I can go back. I use Tailwind on this portfolio so I control the design, bundled with Vite — no CDN on the live site.\n\nNext is the same care for data: seeders so production is not empty, and a case per project instead of only a demo button.",
                ],
            ]
        );

        Blog::updateOrCreate(
            ['slug' => 'waarom-ik-kies-voor-precisie-mijn-visie-op-moderne-webontwikkeling-in-2026'],
            [
                'title' => [
                    'nl' => 'Jungletuinen live zetten leerde me meer dan de tutorial',
                    'en' => 'Shipping Jungle Gardens taught me more than the tutorial',
                ],
                'content' => [
                    'nl' => "Jungle Tuinen begon als een idee: uitleggen hoe je een tuin in lagen opbouwt. In Vue is dat verleidelijk één lange pagina. Dat werd onleesbaar.\n\nIk splitste het in stappen, foto’s en een kaart. Elke pagina moest één vraag beantwoorden. De kaart in Zwolle was leuk om te maken; het nuttiger deel was navigatie die op telefoon niet in de weg zit.\n\nWat ik meeneem: content eerst, daarna componenten. En een publieke URL, anders blijft het een oefening.",
                    'en' => "Jungle Gardens started as an idea: explain how you build a garden in layers. In Vue that easily becomes one long page. That became unreadable.\n\nI split it into steps, photos and a map. Each page had to answer one question. The Zwolle map was fun to build; the useful part was navigation that does not get in the way on a phone.\n\nWhat I take with me: content first, then components. And a public URL, otherwise it stays an exercise.",
                ],
            ]
        );
    }
}
