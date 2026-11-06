@extends('layouts.layout', [
    'title' => __('messages.privacy.title').' — '.config('site.name'),
    'description' => __('messages.privacy.intro'),
])

@section('nav')
    <x-site-nav />
@endsection

@php
    $vars = ['name' => config('site.name'), 'email' => config('site.email'), 'days' => config('site.visits_retention_days')];
@endphp

@section('content')
    <div class="px-4 pb-24 pt-28 md:pt-36">
        <div class="mx-auto max-w-3xl">
            <nav data-reveal class="flex items-center gap-2 text-sm text-ink-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-ink-text">{{ __('messages.hero.crumb') }}</a>
                <span>›</span>
                <span class="text-ink-soft">{{ __('messages.privacy.title') }}</span>
            </nav>
            <h1 data-split class="mt-6 text-4xl font-bold tracking-[-.03em] md:text-6xl">{{ __('messages.privacy.title') }}</h1>
            <p data-reveal class="mt-4 text-lg leading-relaxed text-ink-soft">{{ __('messages.privacy.intro') }}</p>

            <div class="panel mt-12 divide-y divide-ink-line" data-reveal-group>
                @foreach(__('messages.privacy.sections') as $section)
                    <section data-reveal class="grid gap-2 p-6 md:grid-cols-[200px_1fr] md:gap-8">
                        <h2 class="font-semibold">{{ $section['title'] }}</h2>
                        <p class="text-sm leading-relaxed text-ink-soft">{{ __($section['body'], $vars) }}</p>
                    </section>
                @endforeach
            </div>

            <p class="mt-6 text-xs text-ink-muted">{{ __('messages.privacy.updated', ['date' => '06-11-2026']) }}</p>
        </div>
    </div>
@endsection
