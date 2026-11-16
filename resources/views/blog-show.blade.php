@extends('layouts.layout', [
    'title' => \App\Support\Seo::noteTitle($blog),
    'description' => \App\Support\Seo::noteDescription($blog),
    'ogImage' => \App\Support\Seo::ogImage('blog', $blog->id),
    'ogType' => 'article',
    'published' => $blog->publishedDate()->toAtomString(),
    'modified' => ($blog->updated_at ?? $blog->publishedDate())->toAtomString(),
    'crumbs' => [[__('seo.breadcrumbs.notes'), lroute('blog.index')], [$blog->getTranslation('title'), \App\Support\Seo::canonical()]],
    'schema' => [\App\Support\Seo::blogPosting($blog, \App\Support\Seo::canonical())],
    'mainEntity' => \App\Support\Seo::canonical().'#article',
])


@section('content')
    <article>
        <header class="page-head container">
            <a href="{{ lroute('blog.index') }}" class="crumb" data-hero-line style="--i: 0">{{ icon('arrow-left') }} {{ __('site.notes.back') }}</a>
            <h1 class="statement mx-auto mt-6 max-w-[18ch]" data-hero-line style="--i: 1">{{ $blog->getTranslation('title') }}</h1>
            <p class="mt-6 text-small text-muted" data-hero-line style="--i: 2">
                <time datetime="{{ $blog->publishedDate()->toDateString() }}">{{ $blog->publishedDate()->translatedFormat('j F Y') }}</time>
                · {{ __('site.notes.minutes', ['n' => $blog->readingMinutes()]) }}
            </p>
        </header>

        @if($blog->image)
            <figure class="container" data-reveal>
                <img src="{{ asset('storage/'.$blog->image) }}" alt="{{ $blog->getTranslation('title') }}" class="mx-auto aspect-[16/9] w-full max-w-content rounded-1 object-cover" decoding="async" fetchpriority="high">
            </figure>
        @endif

        <div class="container pb-section {{ $blog->image ? 'pt-stack' : '' }}">
            <div class="prose-note read" data-reveal>
                {!! Str::markdown($blog->getTranslation('content')) !!}
            </div>
            <p class="read mt-stack border-t border-line pt-8">
                <a href="{{ lroute('blog.index') }}" class="link-arrow"><span>{{ __('site.notes.all') }}</span>{{ icon('arrow-right') }}</a>
            </p>
        </div>
    </article>
@endsection
