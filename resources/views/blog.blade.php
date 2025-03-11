@extends('layouts.app')
@section('title', 'Amir Jebbari:home')
@section('content')
    <div class="textbackgroundblog">
    <div class="textcontentblog">
        <p></p>
        <h1> <br><span id="blogs">Mijn eerste ervaringen met laravel, PHP en Frontend Development</span></h1><br><style>
            #blogs{
                display: flex;
                justify-items: center;
            }
        </style>
        <p>Als eerstejaars software development student ben ik de afgelopentijd veel bezig geweest met <strong>Laravel, PHP, git, html, css en javascript</strong>Het is een heleboel om te leren, maar ik begin steeds beter te begrijpen hoe alles samenwerkt</p><br>
        <h2>Werken met Laravel</h2>
        <p>Laravel is mijn eerste ervaring met een PHP-framework, en ik vind het indrukwekkend hoe het veel complexe taken vereenvoudigt. Routing, databases en authenticatie zijn makkelijker te beheren dan wanneer je alles vanaf nul zou moeten coderen.</p><br>
            <code>Route::get('/posts', [PostController::class, 'index']);
        </code>
        <p>Dit zorgt ervoor dat wanneer iemand de pagina <code>/posts</code> bezoekt, de <code>index</code>-methode in de <code>PostController</code> wordt uitgevoerd.</p>

        <h2>Frontend met HTML, CSS en JavaScript</h2><br>
        <p>Natuurlijk werk ik ook aan de frontend. HTML en CSS ken ik al een beetje, maar ik leer nu ook <strong>meer over JavaScript</strong> om interactieve pagina’s te maken.</p>
        <code>
            document.getElementById("myButton").addEventListener("click", function() {
            alert("Button clicked!");
            });
        </code>
        <p>Dit laat een alert zien als iemand op de knop klikt. Kleine dingen, maar ze maken een groot verschil in de gebruikerservaring.</p>

        <h2>Versiebeheer met Git</h2><br>
        <p>Git is een andere tool waar ik veel mee werk. Het is even wennen, maar het gebruik van <code>git commit</code> en <code>git push</code> voelt steeds natuurlijker.</p>
        <code>
            git checkout -b nieuwe-feature
            git add .
            git commit -m "Nieuwe feature toegevoegd"
            git push origin nieuwe-feature
        </code>
        <p>Het werken met Git maakt samenwerken en versiebeheer een stuk overzichtelijker.</p>

        <h2>Wat komt hierna?</h2><br>
        <p>Ik kijk ernaar uit om <strong>meer te leren over databases, API’s en complexe projecten</strong>. Mijn doel is om een volledig werkende webapplicatie te maken waarin <strong>Laravel en JavaScript samenkomen</strong>. Misschien schrijf ik daar binnenkort een update over!</p><br>

        <p>🚀 Heb jij tips of ervaringen met Laravel en frontend development? Laat het me weten!</p>
    </div>

    </div>
    <a href="{{ route('blogEN') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
            </svg>
        </button>
    </a>
@endsection