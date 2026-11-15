@extends('layouts.layout', [
    'title' => ($blog->meta_title ?: $blog->getTranslation('title')).' — Amir Jebbari',
    'description' => $blog->meta_description ?: $blog->excerpt(200),
    'ogImage' => route('og', ['type' => 'blog', 'id' => $blog->id, 'lang' => app()->getLocale()]),
])

@push('head')
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $blog->getTranslation('title'),
        'description' => $blog->meta_description ?: $blog->excerpt(200),
        'datePublished' => $blog->publishedDate()->toAtomString(),
        'dateModified' => $blog->updated_at?->toAtomString(),
        'url' => url()->current(),
        'inLanguage' => app()->getLocale(),
        'image' => $blog->image ? asset('storage/'.$blog->image) : null,
        'author' => ['@type' => 'Person', '@id' => url('/').'#person', 'name' => config('site.name'), 'url' => lroute('home')],
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

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
                <img src="{{ asset('storage/'.$blog->image) }}" alt="" class="mx-auto aspect-[16/9] w-full max-w-content rounded-1 object-cover" decoding="async" fetchpriority="high">
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
