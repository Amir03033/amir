@extends('layouts.app')
@section('title', 'Over mij')
@section('content')
    <div class="textbackground">
        <div class="textcontentabout">
            <h1>Ik ben <span> Amir Jebbari</span></h1>

            <p>
                Mijn naam is <strong>Amir Jebbari</strong>, ik ben 18 jaar oud en woon in Zwolle.
                Als enig kind heb ik altijd veel ruimte en vrijheid gehad om mezelf te ontdekken
                en mijn interesses te ontwikkelen. Hierdoor heb ik een brede kijk op het leven
                gekregen en weet ik goed wat mij motiveert.
            </p>

            <p>
                In mijn vrije tijd ben ik veel bezig met <strong>fitness</strong>. Ik vind het belangrijk
                om zowel fysiek als mentaal sterk te blijven. Daarnaast onderneem ik graag activiteiten
                met vrienden — van samen sporten tot gewoon gezellig tijd doorbrengen. Deze sociale
                momenten geven mij energie en zorgen voor een goede balans in mijn leven.
            </p>

            <p>
                Een grote passie van mij is <strong>programmeren</strong>. Ik werk graag aan projecten en
                los technische problemen op. Het geeft mij voldoening om iets vanaf nul op te bouwen
                en mezelf steeds verder te ontwikkelen. Ik zie hierin ook veel kansen voor mijn toekomst.
            </p>

            <p>
                Daarnaast heb ik een <strong>Iraanse achtergrond</strong>, waar ik erg trots op ben.
                Mijn cultuur en afkomst spelen een belangrijke rol in wie ik ben. Het heeft mij gevormd,
                mijn normen en waarden beïnvloed en geeft mij een unieke identiteit die ik overal met
                me meedraag.
            </p>
        </div>
    </div>
    <a href="{{ route('aboutEN') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802"/>
            </svg>
        </button>
    </a>
@endsection