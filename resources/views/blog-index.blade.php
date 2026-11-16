@extends('layouts.layout', [
    'title' => __('seo.pages.notes.title'),
    'description' => __('seo.pages.notes.description'),
    'ogImage' => \App\Support\Seo::ogImage('page', 'notes'),
    'pageType' => 'CollectionPage',
    'crumbs' => [[__('seo.breadcrumbs.notes'), lroute('blog.index')]],
    'schema' => [\App\Support\Seo::itemList(\App\Support\Seo::canonical(), $blogs->map(fn ($b) => [$b->getTranslation('title'), lroute('blog.show', $b->slug)]))],
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
