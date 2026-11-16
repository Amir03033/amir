@extends('layouts.layout', [
    'title' => __('seo.pages.roadmap.title'),
    'description' => __('seo.pages.roadmap.description'),
    'ogImage' => \App\Support\Seo::ogImage('page', 'roadmap'),
    'pageType' => 'CollectionPage',
    'crumbs' => [[__('seo.breadcrumbs.roadmap'), lroute('roadmap')]],
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('site.ideas.label') }}</p>
        <h1 class="hero-type mx-auto mt-5" data-hero-line style="--i: 1">{{ __('site.ideas.roadmap_title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('site.ideas.roadmap_lead') }}</p>
    </header>

    <div class="container pb-section">
        <div class="read">
            @php($any = $groups->flatten()->isNotEmpty())
            @unless($any)
                <p class="empty">{{ __('site.ideas.empty') }}</p>
            @endunless

            @foreach(['building', 'planned', 'new', 'shipped'] as $status)
                @if($groups[$status]->isNotEmpty())
                    <section class="mt-stack first:mt-0" aria-labelledby="status-{{ $status }}">
                        <h2 id="status-{{ $status }}" class="text-title font-semibold" data-reveal>{{ __('site.ideas.status.'.$status) }} <span class="text-muted tabular">{{ $groups[$status]->count() }}</span></h2>
                        <p class="mt-2 text-small text-muted" data-reveal>{{ __('site.ideas.status_lead.'.$status) }}</p>
                        <ul class="rule-list mt-6" data-reveal-group>
                            @foreach($groups[$status] as $idea)
                                <x-idea-row :idea="$idea" :voted="in_array($idea->id, $voted, true)" />
                            @endforeach
                        </ul>
                    </section>
                @endif
            @endforeach

            @if($groups['declined']->isNotEmpty())
                <details class="mt-stack">
                    <summary class="cursor-pointer text-fg-2 hover:text-fg">{{ __('site.ideas.status.declined') }} <span class="tabular text-muted">{{ $groups['declined']->count() }}</span></summary>
                    <ul class="rule-list mt-6">
                        @foreach($groups['declined'] as $idea)
                            <x-idea-row :idea="$idea" />
                        @endforeach
                    </ul>
                </details>
            @endif

            <section class="mt-section border-t border-line pt-stack" aria-labelledby="suggest-title">
                <h2 id="suggest-title" class="text-title font-semibold" data-reveal>{{ __('site.ideas.title') }}</h2>
                <p class="mt-3 text-fg-2" data-reveal>{{ __('site.ideas.lead') }}</p>
                <div class="mt-10" data-reveal>@include('partials.idea-form')</div>
            </section>
        </div>
    </div>
@endsection
