@php
    $pageTitle = $title ?? config('site.name').' — '.__('messages.home.subtitle');
    $pageDescription = $description ?? __('messages.meta.description');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F7F5F1">
    <meta name="color-scheme" content="light dark">
    <script>
        // Theme before first paint (no flash). Preference: "system" (default), "light" or "dark".
        (function (d) {
            var mq = matchMedia('(prefers-color-scheme: dark)'), pref = null;
            try { pref = localStorage.getItem('theme'); } catch (e) {}
            if (pref !== 'light' && pref !== 'dark') pref = 'system';
            function apply(p) {
                var t = p === 'system' ? (mq.matches ? 'dark' : 'light') : p;
                d.dataset.theme = t;
                d.dataset.themePref = p;
                var m = document.querySelector('meta[name=theme-color]');
                if (m) m.content = t === 'dark' ? '#121110' : '#F7F5F1';
                return t;
            }
            apply(pref);
            window.__theme = {
                pref: function () { return d.dataset.themePref; },
                set: function (p) {
                    try { p === 'system' ? localStorage.removeItem('theme') : localStorage.setItem('theme', p); } catch (e) {}
                    window.dispatchEvent(new CustomEvent('themechange', { detail: apply(p) }));
                },
            };
            mq.addEventListener('change', function () { if (d.dataset.themePref === 'system') window.__theme.set('system'); });

            // Reveal targets start hidden only when motion is allowed and JS runs; un-hide if the bundle never starts.
            if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
                d.classList.add('js-motion');
                setTimeout(function () { if (!d.classList.contains('motion-ready')) d.classList.remove('js-motion'); }, 2500);
            }
        })(document.documentElement);
    </script>
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="{{ config('site.name') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="nl" href="{{ alternate_url('nl') }}">
    <link rel="alternate" hreflang="en" href="{{ alternate_url('en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ alternate_url('nl') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preload" href="{{ asset('fonts/instrument-sans-latin-wght.woff2') }}" as="font" type="font/woff2" crossorigin>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="amirs.life">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'nl_NL' }}">
    <meta property="og:image" content="{{ $ogImage ?? \App\Models\Setting::media('og', 'images/og-default.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a href="#main" class="skip-link btn-primary">{{ __('messages.nav.skip') }}</a>

@hasSection('nav')
    @yield('nav')
@else
    <x-site-nav />
@endif

<main id="main" tabindex="-1" class="outline-none">
    @yield('content')
</main>

@hasSection('compact-footer')
    <x-site-footer :mail="false" />
@else
    <x-site-footer />
@endif
<x-toast-region />
{{-- ⌘K / Ctrl K opens the terminal toy. The home page has it inline instead. --}}
@unless(View::hasSection('inline-terminal'))
    <dialog id="terminal-dialog" class="dialog term-dialog" aria-label="{{ __('site.terminal.label') }}">
        <div class="dialog-head"><p class="label">{{ __('site.terminal.label') }}</p><button type="button" class="btn-icon" data-dialog-close aria-label="{{ __('messages.nav.close') }}">{{ icon('x') }}</button></div>
        <div class="dialog-body"><x-terminal id="term-dialog" /></div>
    </dialog>
@endunless
</body>
</html>
