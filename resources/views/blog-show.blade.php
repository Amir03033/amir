@extends('layouts.layout', [
    'title' => ($blog->meta_title ?: $blog->getTranslation('title')).' — '.config('site.name'),
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
        'author' => ['@type' => 'Person', 'name' => config('site.name'), 'url' => lroute('home')],
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <article class="px-4 pb-24 pt-28 md:pt-36">
        <div class="mx-auto max-w-3xl">
            <nav data-reveal class="flex flex-wrap items-center gap-2 text-sm text-ink-muted" aria-label="Breadcrumb">
                <a href="{{ lroute('home') }}" class="hover:text-ink-text">{{ __('messages.hero.crumb') }}</a>
                <span>›</span>
                <a href="{{ lroute('blog.index') }}" class="hover:text-ink-text">{{ __('messages.nav.blog') }}</a>
                <span>›</span>
                <span class="truncate text-ink-soft">{{ $blog->getTranslation('title') }}</span>
            </nav>

            <p data-reveal class="mt-8 text-xs text-ink-muted">{{ __('messages.blog.written') }} · <time datetime="{{ $blog->publishedDate()->toDateString() }}">{{ $blog->publishedDate()->format('Y-m-d') }} · {{ __('messages.blog.minutes', ['n' => $blog->readingMinutes()]) }}</time></p>
            <h1 data-split class="mt-3 text-3xl font-semibold leading-tight tracking-tight md:text-5xl">{{ $blog->getTranslation('title') }}</h1>

            @if($blog->image)
                <div data-reveal class="panel mt-10 overflow-hidden">
                    <img src="{{ asset('storage/'.$blog->image) }}" alt="" class="aspect-[16/9] w-full object-cover">
                </div>
            @endif

            <div data-reveal class="prose-scope mt-12 border-t border-ink-line pt-10 text-ink-soft md:prose-lg">
                {!! Str::markdown($blog->getTranslation('content')) !!}
            </div>

            <div class="mt-20 border-t border-ink-line pt-8">
                <a href="{{ lroute('blog.index') }}" class="group inline-flex items-center gap-2 text-sm text-scope-accent">
                    <span class="transition-transform group-hover:-translate-x-1">←</span> {{ __('messages.blog.all') }}
                </a>
            </div>
        </div>
    </article>
@endsection
