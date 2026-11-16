@extends('layouts.layout', [
    'title' => __('messages.cv.title').' — Amir Jebbari',
    'description' => __('messages.cv.intro'),
])

@php($hasServerScope = $projects->contains(fn ($p) => $p->slug === 'serverscope'))

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('messages.cv.title') }}</p>
        <h1 class="hero-type mx-auto mt-5" data-hero-line style="--i: 1">{{ config('site.name') }}</h1>
        <p class="mx-auto mt-6 max-w-xl text-fg-2" data-hero-line style="--i: 2">{{ seo_sentence(link: true) }}</p>
        <div class="no-print mt-10 flex flex-wrap justify-center gap-3" data-hero-line style="--i: 3">
            <a href="{{ lroute('cv.pdf') }}" class="btn-primary">{{ icon('file-text') }} {{ __('messages.cv.download') }}</a>
            <button type="button" onclick="window.print()" class="btn-secondary">{{ __('messages.cv.print') }}</button>
        </div>
    </header>

    <div class="container pb-section">
        <div class="read">
            @foreach(['skills', 'projects', 'contact'] as $block)
                <section class="grid gap-4 border-t border-line py-10 md:grid-cols-[10rem_1fr]" data-reveal>
                    <h2 class="label">{{ $block === 'skills' ? __('messages.skills.label') : __('messages.cv.'.$block) }}</h2>
                    <div>
                        @switch($block)
                            @case('skills')
                                <div class="space-y-2 text-small">
                                    @foreach(__('messages.skills.groups') as $group)
                                        <p><span class="text-muted">{{ $group['label'] }}:</span> {{ implode(', ', $group['items']) }}</p>
                                    @endforeach
                                </div>
                                @break
                            @case('projects')
                                <div class="space-y-8">
                                    @unless($hasServerScope)
                                        <div>
                                            <h3 class="font-semibold">ServerScope</h3>
                                            <p class="mt-1 text-small text-fg-2">{{ __('messages.serverscope.tagline') }}</p>
                                            <p class="mt-1.5 text-small text-muted">{{ implode(' · ', __('messages.serverscope.stack')) }}</p>
                                        </div>
                                    @endunless
                                    @foreach($projects as $project)
                                        <div>
                                            <h3 class="font-semibold"><a href="{{ lroute('project.show', $project) }}" class="link">{{ $project->getTranslation('title') }}</a>@if($project->year) <span class="text-small font-normal text-muted tabular">· {{ $project->year }}</span>@endif</h3>
                                            <p class="mt-1 text-small text-fg-2">{{ $project->oneLiner() }}</p>
                                            <p class="mt-1.5 text-small text-muted">{{ implode(' · ', $project->tagList()) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                @break
                            @case('contact')
                                <div class="space-y-1.5 text-small">
                                    <a class="link block w-fit" href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                                    @if(config('site.phone'))
                                        <a class="link block w-fit" href="tel:{{ config('site.phone_href') }}">{{ config('site.phone') }}</a>
                                    @endif
                                    <a class="link block w-fit" href="{{ config('site.linkedin') }}" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                                    @if(config('site.github'))
                                        <a class="link block w-fit" href="{{ config('site.github') }}" target="_blank" rel="noopener noreferrer">GitHub</a>
                                    @endif
                                    @if(config('site.jebbari.url'))
                                        <a class="link block w-fit" href="{{ config('site.jebbari.url') }}" target="_blank" rel="noopener">Jebbari</a>
                                    @endif
                                </div>
                                @break
                        @endswitch
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
