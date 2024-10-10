@extends('layouts.app')
@section('title', 'Over mij')
@section('content')

    <div class="text-content">
        <p>Hallo!</p>
        <h1>Ik ben <span>Amir Jebbari</span></h1>
        <p>
            Mijn naam is Amir Jebbari, ik ben 16 jaar en woon in Zwolle. Als enig kind heb ik altijd de ruimte gehad om
            mijn passies te ontwikkelen. Mijn hobby's zijn fitness, het ondernemen van activiteiten met vrienden, en
            natuurlijk programmeren. Daarnaast heb ik een Iraanse achtergrond, iets waar ik trots op ben en wat een
            belangrijke rol speelt in mijn identiteit.</p>
{{--        <div class="buttons">--}}
{{--            <a href="#" class="btn"><i class="fas fa-user"></i> More About Me</a>--}}
{{--            <a href="#" class=" btn-portofolio btn"><i class="fas fa-briefcase"></i>Portfolio</a>--}}
{{--        </div>--}}
    </div>
    <a href="{{ route('aboutEN') }}">
        <button class="slide-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
            </svg>
        </button>
    </a>
@endsection