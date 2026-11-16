@php
    $via = in_array(request('via'), ['nav', 'home', 'case', 'post', 'footer', 'hero', 'about'], true) ? request('via') : null;
    $email = config('seo.email');
@endphp

@extends('layouts.layout', [
    'title' => __('site.wwm.meta_title'),
    'description' => __('site.wwm.meta_description'),
    'ogImage' => \App\Support\Seo::ogImage('page', 'work-with-me'),
    'crumbs' => [[__('site.wwm.label'), lroute('work-with-me')]],
    'schema' => [[
        '@type' => 'ItemList',
        '@id' => \App\Support\Seo::canonical().'#services',
        'name' => __('site.wwm.what_title'),
        'itemListElement' => collect(__('site.wwm.services'))->keys()->values()->map(fn ($key, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => __('site.wwm.services.'.$key.'.0'),
            'url' => rtrim(config('seo.company.url'), '/').(app()->getLocale() === 'en' ? '/en/services/' : '/nl/diensten/').config('seo.company.services.'.$key.'.'.app()->getLocale()),
        ])->all(),
    ]],
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('site.wwm.label') }}</p>
        <h1 class="hero-type mx-auto mt-5 max-w-[12ch]" data-hero-line style="--i: 1">{{ __('site.wwm.title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('site.wwm.lead') }}</p>
        <div class="mt-10 flex flex-col items-center gap-4" data-hero-line style="--i: 3">
            <a href="{{ jebbari_url('request', 'work-with-me', content: $via) }}" class="btn-primary" rel="noopener"
               data-track="cta_jebbari" data-track-label="work-with-me{{ $via ? ':'.$via : '' }}">{{ __('site.wwm.primary') }} {{ icon('arrow-up-right') }}</a>
            <p class="text-small text-muted">{{ __('site.wwm.primary_note') }}</p>
            <a href="mailto:{{ $email }}" class="link text-small" data-track="cta_mail" data-track-label="work-with-me">{{ __('site.wwm.secondary', ['email' => $email]) }}</a>
        </div>
    </header>

    <div class="container pb-section">
        <div class="read">
            <section class="border-t border-line py-stack" aria-labelledby="who">
                <h2 id="who" class="text-title font-semibold" data-reveal>{{ __('site.wwm.who_title') }}</h2>
                <div class="mt-6 space-y-4 text-lead text-fg-2" data-reveal-group>
                    @foreach(__('site.wwm.who') as $line)
                        <p data-reveal>{{ $line }}</p>
                    @endforeach
                </div>
            </section>

            <section class="border-t border-line py-stack" aria-labelledby="what">
                <h2 id="what" class="text-title font-semibold" data-reveal>{{ __('site.wwm.what_title') }}</h2>
                <ul class="rule-list mt-8" data-reveal-group>
                    @foreach(__('site.wwm.services') as $key => [$name, $line])
                        <li data-reveal>
                            <a href="{{ jebbari_url('service:'.$key, 'work-with-me') }}" class="group grid grid-cols-[1fr_auto] items-baseline gap-4 py-5" rel="noopener"
                               data-track="cta_jebbari" data-track-label="work-with-me:service">
                                <span>
                                    <span class="block text-h3 font-semibold decoration-line-strong decoration-1 underline-offset-[.2em] group-hover:underline">{{ $name }}</span>
                                    <span class="mt-1 block text-small text-muted">{{ $line }}</span>
                                </span>
                                {{ icon('arrow-up-right', 'text-muted') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-6"><a href="{{ jebbari_url('services', 'work-with-me') }}" class="link-arrow" rel="noopener" data-track="cta_jebbari" data-track-label="work-with-me:services"><span>{{ __('site.wwm.more') }}</span>{{ icon('arrow-up-right', 'icon-diag') }}</a></p>
            </section>

            <section class="border-t border-line py-stack" aria-labelledby="how">
                <h2 id="how" class="text-title font-semibold" data-reveal>{{ __('site.wwm.how_title') }}</h2>
                <ol class="mt-8 space-y-8" data-reveal-group>
                    @foreach(__('site.wwm.steps') as $i => [$name, $text])
                        <li class="grid grid-cols-[2.5rem_1fr] gap-4" data-reveal>
                            <span class="text-h3 font-semibold text-muted tabular">{{ $i + 1 }}</span>
                            <span><span class="block text-h3 font-semibold">{{ $name }}</span><span class="mt-1 block text-fg-2">{{ $text }}</span></span>
                        </li>
                    @endforeach
                </ol>
            </section>

            @if($projects->isNotEmpty())
                <section class="border-t border-line py-stack" aria-labelledby="built">
                    <h2 id="built" class="text-title font-semibold" data-reveal>{{ __('site.wwm.work_title') }}</h2>
                    <ul class="project-list mt-8" data-reveal-group>
                        @foreach($projects as $project)
                            <x-project-row :project="$project" />
                        @endforeach
                    </ul>
                    <p class="mt-6"><a href="{{ lroute('work') }}" class="link-arrow"><span>{{ __('site.wwm.work_link') }}</span>{{ icon('arrow-right') }}</a></p>
                </section>
            @endif

            <section class="border-t border-line pt-stack text-center" aria-labelledby="go">
                <h2 id="go" class="statement" data-reveal>{{ __('site.wwm.title') }}</h2>
                <p class="mt-8" data-reveal>
                    <a href="{{ jebbari_url('request', 'work-with-me', content: 'bottom') }}" class="btn-primary" rel="noopener"
                       data-track="cta_jebbari" data-track-label="work-with-me:bottom">{{ __('site.wwm.primary') }} {{ icon('arrow-up-right') }}</a>
                </p>
                <p class="mt-4"><a href="mailto:{{ $email }}" class="link text-small" data-track="cta_mail" data-track-label="work-with-me:bottom">{{ __('site.wwm.secondary', ['email' => $email]) }}</a></p>
            </section>
        </div>
    </div>
@endsection
