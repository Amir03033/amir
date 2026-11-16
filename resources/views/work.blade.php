@extends('layouts.layout', [
    'title' => __('seo.pages.work.title'),
    'description' => __('seo.pages.work.description'),
    'ogImage' => \App\Support\Seo::ogImage('page', 'work'),
    'pageType' => 'CollectionPage',
    'crumbs' => [[__('seo.breadcrumbs.work'), lroute('work')]],
    'schema' => [\App\Support\Seo::itemList(\App\Support\Seo::canonical(), $projects->map(fn ($p) => [$p->getTranslation('title'), lroute('project.show', $p)]))],
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ trans_choice('messages.portfolio.count', $projects->count(), ['count' => $projects->count()]) }}</p>
        <h1 class="hero-type mx-auto mt-5 max-w-[12ch]" data-hero-line style="--i: 1">{{ __('site.work.title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('site.work.index_lead') }}</p>
    </header>

    <section class="container pb-section" x-data="{ tag: '' }" aria-label="{{ __('site.work.index_title') }}">
        @if($tags->count() > 1)
            <div class="filter mb-12" role="group" aria-label="{{ __('site.work.filter_label') }}" data-reveal>
                <button type="button" @click="tag = ''" :aria-pressed="(tag === '').toString()" aria-pressed="true">{{ __('site.work.filter_all') }}</button>
                @foreach($tags as $t)
                    <button type="button" @click="tag = @js(strtolower($t))" :aria-pressed="(tag === @js(strtolower($t))).toString()" aria-pressed="false">{{ $t }}</button>
                @endforeach
            </div>
        @endif

        @if($projects->isEmpty())
            <p class="empty">{{ __('site.work.empty') }}</p>
        @else
            <ul class="project-list read !max-w-content" data-reveal-group>
                @foreach($projects as $project)
                    <x-project-row :project="$project" x-show="!tag || {{ \Illuminate\Support\Js::from(array_map('strtolower', $project->tagList())) }}.includes(tag)" />
                @endforeach
            </ul>
        @endif
    </section>
@endsection
