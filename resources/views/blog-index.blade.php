@extends('layouts.layout', [
    'title' => __('messages.blog.index_title').' — '.config('site.name'),
    'description' => __('messages.blog.index_intro'),
])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <div class="px-4 pb-24 pt-28 md:pt-36">
        <div class="mx-auto max-w-4xl">
            <nav data-reveal class="flex items-center gap-2 text-sm text-ink-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-ink-text">{{ __('messages.hero.crumb') }}</a>
                <span>›</span>
                <span class="text-ink-soft">{{ __('messages.blog.index_title') }}</span>
            </nav>
            <h1 data-split class="mt-6 text-4xl font-bold tracking-[-.03em] md:text-6xl">{{ __('messages.blog.index_title') }}</h1>
            <p data-reveal class="mt-4 max-w-xl text-lg text-ink-soft">{{ __('messages.blog.index_intro') }}</p>

            <div class="mt-12">
                @if($blogs->isNotEmpty())
                    <x-blog-list :blogs="$blogs" />
                @else
                    <p class="panel p-8 text-center text-sm text-ink-muted">{{ __('messages.blog.empty') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
