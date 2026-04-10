@extends('layouts.app')
@section('title', 'Amir Jebbari')
@section('content')
    <div class="BackgroundIndex">
<div class="text-content">
    <h1>Ik ben <span>Amir Jebbari</span></h1>
    <p>
        Ik ben <strong>Amir Jebbari</strong>, een gedreven softwareontwikkelaar aan het begin van mijn carrière.
        Momenteel ben ik actief bezig met het ontwikkelen van mijn vaardigheden binnen webontwikkeling,
        waarbij ik werk met HTML en CSS als basis, maar ook steeds meer ervaring opdoe met moderne
        technologieën zoals Node.js, Vue.js en Laravel.
    </p>

    <p>
        Ik vind het interessant om te werken met <strong>API’s</strong> en leer hoe ik dynamische en interactieve
        applicaties kan bouwen die zowel functioneel als gebruiksvriendelijk zijn. Door praktijkgericht
        te werken aan projecten blijf ik mezelf continu uitdagen en verbeteren.
    </p>

    <p>
        Terwijl ik mijn opleiding voortzet, richt ik me op het verdiepen van mijn kennis en het verbreden
        van mijn technische stack. Met een sterke nieuwsgierigheid en een duidelijke motivatie om te groeien,
        ben ik vastberaden om mezelf te ontwikkelen tot een professionele developer en een waardevolle
        bijdrage te leveren binnen de tech-industrie.
    </p>
    <div class="buttons">
        <a href="{{ route('contact') }}" class="btn"><i class="fas fa-user"></i> Contact</a>
        <a href="{{ route('portfolio') }}" class=" btn-portofolio btn"><i class="fas fa-briefcase"></i>Portfolio</a>
    </div>
    <a href="{{ route('index-en') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
            </svg>
        </button>
    </a>
{{--    <li><a href="{{ route('home') }}" class="active"><i class="fas fa-home"></i> <span>Home</span></a></li>--}}

</div>
    </div>
{{--<div class="image-content">--}}
{{--    <img src="Amirfoto%20copy.png" alt="Amirfoto">--}}
{{--</div>--}}
@endsection
