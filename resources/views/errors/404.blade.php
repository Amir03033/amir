@extends('layouts.layout', ['title' => '404 — amirs.life'])

@section('content')
    <section class="container flex min-h-[88svh] flex-col items-center justify-center pt-[var(--nav-h)] text-center">
        <p class="label" data-hero-line style="--i: 0">404</p>
        <h1 class="statement mt-5 max-w-[16ch]" data-hero-line style="--i: 1">{{ __('messages.errors.not_found') }}</h1>
        <p class="lead mt-6 max-w-md" data-hero-line style="--i: 2">{{ __('messages.errors.not_found_text') }}</p>
        <div class="mt-10 flex flex-wrap justify-center gap-3" data-hero-line style="--i: 3">
            <a href="{{ lroute('home') }}" class="btn-primary">{{ __('messages.errors.home') }}</a>
            <button type="button" onclick="history.back()" class="btn-secondary">{{ __('messages.errors.back') }}</button>
        </div>
    </section>
@endsection
