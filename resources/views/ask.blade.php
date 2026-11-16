@extends('layouts.layout', [
    'title' => __('seo.pages.ask.title'),
    'description' => __('seo.pages.ask.description'),
    'ogImage' => \App\Support\Seo::ogImage('page', 'ask'),
    'pageType' => 'CollectionPage',
    'crumbs' => [[__('seo.breadcrumbs.ask'), lroute('ask')]],
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('site.ask.label') }}</p>
        <h1 class="hero-type mx-auto mt-5 max-w-[12ch]" data-hero-line style="--i: 1">{{ __('site.ask.title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('site.ask.lead') }}</p>
    </header>

    <div class="container pb-section">
        <div class="read">
            <div id="ask-form" class="scroll-mt-24" data-reveal>@include('partials.ask-form')</div>

            <div class="rule-list mt-stack">
                @forelse($questions as $question)
                    <x-question-item :question="$question" :level="2" />
                @empty
                    <p class="empty">{{ __('site.ask.empty') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
