@extends('layouts.layout', [
    'title' => __('site.certificates.label').' — Amir Jebbari',
    'description' => __('site.certificates.lead'),
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('site.certificates.label') }}</p>
        <h1 class="hero-type mx-auto mt-5 max-w-[12ch]" data-hero-line style="--i: 1">{{ __('site.certificates.title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('site.certificates.lead') }}</p>
    </header>

    <section class="container pb-section">
        @if($certificates->isEmpty())
            <p class="empty">{{ __('site.certificates.empty') }}</p>
        @else
            <ul class="rule-list read" data-reveal-group>
                @foreach($certificates as $cert)
                    <x-certificate-row :cert="$cert" />
                @endforeach
            </ul>
        @endif
    </section>
@endsection
