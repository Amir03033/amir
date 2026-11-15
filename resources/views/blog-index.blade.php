@extends('layouts.layout', [
    'title' => __('site.notes.label').' — Amir Jebbari',
    'description' => __('site.notes.lead'),
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('site.notes.label') }}</p>
        <h1 class="hero-type mx-auto mt-5 max-w-[12ch]" data-hero-line style="--i: 1">{{ __('site.notes.title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('site.notes.lead') }}</p>
    </header>

    <section class="container pb-section">
        @if($blogs->isEmpty())
            <p class="empty">{{ __('site.notes.empty') }}</p>
        @else
            <ul class="rule-list read" data-reveal-group>
                @foreach($blogs as $blog)
                    <x-note-row :blog="$blog" />
                @endforeach
            </ul>
        @endif
    </section>
@endsection
