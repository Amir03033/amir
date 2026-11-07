@php
    $pageTitle = $title ?? config('site.name').' — '.__('messages.home.subtitle');
    $pageDescription = $description ?? __('messages.meta.description');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#09090b">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="{{ config('site.name') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="nl" href="{{ alternate_url('nl') }}">
    <link rel="alternate" hreflang="en" href="{{ alternate_url('en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ alternate_url('nl') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'nl_NL' }}">
    <meta property="og:image" content="{{ $ogImage ?? \App\Models\Setting::media('og', 'images/og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <script>
        // Theme before first paint (no flash). Dark is the default; a saved choice wins.
        (function (d) {
            var t = 'dark';
            try { t = localStorage.getItem('theme') || 'dark'; } catch (e) {}
            d.dataset.theme = t;
            window.toggleTheme = function () {
                var next = d.dataset.theme === 'light' ? 'dark' : 'light';
                d.dataset.theme = next;
                try { localStorage.setItem('theme', next); } catch (e) {}
                document.querySelectorAll('[data-theme-src]').forEach(function (img) { img.src = img.dataset.themeSrc + next; });
                document.querySelector('meta[name=theme-color]').content = next === 'light' ? '#fafafa' : '#09090b';
                window.dispatchEvent(new CustomEvent('themechange', { detail: next }));
            };
        })(document.documentElement);

        // Hide reveal targets before first paint; un-hide if the animation bundle never starts.
        (function (d) {
            if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            d.classList.add('js-motion');
            try { if (sessionStorage.getItem('booted') === '1') d.classList.add('booted'); } catch (e) {}
            setTimeout(function () { if (!d.classList.contains('motion-ready')) d.classList.remove('js-motion'); }, 2500);
        })(document.documentElement);
    </script>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{ mobileOpen: false }"
    class="overflow-x-hidden"
>
<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-scope-accent focus:px-4 focus:py-2 focus:text-ink-bg">{{ __('messages.nav.skip') }}</a>

<div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(50%_40%_at_85%_-5%,rgb(110_156_247/.07),transparent),radial-gradient(40%_35%_at_5%_0%,rgb(74_222_128/.04),transparent)]"></div>
<div class="cursor-glow" aria-hidden="true"></div>
<div data-progress class="fixed inset-x-0 top-0 z-[60] h-[2px] origin-left scale-x-0 bg-gradient-to-r from-scope-accent to-scope-green" aria-hidden="true"></div>

@yield('nav')

<main id="main" class="relative">
    @yield('content')
</main>

<x-site-footer />
<x-command-palette />
</body>
</html>
