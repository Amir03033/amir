@extends('layouts.layout', [
    'title' => __('seo.pages.privacy.title'),
    'description' => __('seo.pages.privacy.description'),
    'ogImage' => \App\Support\Seo::ogImage('page', 'privacy'),
    'crumbs' => [[__('seo.breadcrumbs.privacy'), lroute('privacy')]],
])

@php($vars = ['name' => config('site.name'), 'email' => config('site.email'), 'days' => config('site.visits_retention_days')])

@section('content')
    <header class="page-head container">
        <h1 class="hero-type" data-hero-line style="--i: 0">{{ __('messages.privacy.title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 1">{{ __('messages.privacy.intro') }}</p>
    </header>

    <div class="container pb-section">
        <div class="read">
            @foreach(__('messages.privacy.sections') as $section)
                <section class="grid gap-3 border-t border-line py-8 md:grid-cols-[12rem_1fr] md:gap-8" data-reveal>
                    <h2 class="font-semibold">{{ $section['title'] }}</h2>
                    <p class="text-fg-2">{{ __($section['body'], $vars) }}</p>
                </section>
            @endforeach
            <p class="border-t border-line pt-6 text-small text-muted">{{ __('messages.privacy.updated', ['date' => '16-11-2026']) }}</p>
        </div>
    </div>
@endsection
