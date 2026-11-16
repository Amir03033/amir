@php
    // Per-page SEO input (all optional): $title, $description, $ogImage, $pageType, $crumbs, $schema,
    // $mainEntity, $ogType, $noindex, $published, $modified. See SEO.md "Adding a page".
    $locale = app()->getLocale();
    $pageTitle = \App\Support\Seo::title($title ?? __('seo.pages.home.title'));
    $pageDescription = \App\Support\Seo::description($description ?? __('seo.pages.home.description'));
    $canonical = \App\Support\Seo::canonical();
    $alternates = \App\Support\Seo::alternates();
    // A previewed draft (only the logged-in owner gets here) is never indexed and carries no structured data.
    $isDraft = isset($draft) && $draft !== null;
    $noindex = ($noindex ?? false) || $isDraft;
    $robots = $noindex || \App\Support\Seo::isNoindex() ? 'noindex, follow' : 'index, follow, max-image-preview:large';
    $shareImage = $ogImage ?? \App\Models\Setting::media('og', 'images/og-default.png');
    $graph = ($noindex ?? false) ? null : \App\Support\Seo::graph([
        'type' => $pageType ?? 'WebPage',
        'title' => $pageTitle,
        'description' => $pageDescription,
        'canonical' => $canonical,
        'image' => $shareImage,
        'crumbs' => $crumbs ?? [],
        'nodes' => $schema ?? [],
        'mainEntity' => $mainEntity ?? null,
    ]);
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
            // Own key: Filament (/amir, same domain) keeps its panel theme under "theme".
            try { pref = localStorage.getItem('site-theme'); } catch (e) {}
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
                    try { p === 'system' ? localStorage.removeItem('site-theme') : localStorage.setItem('site-theme', p); } catch (e) {}
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
    <meta name="author" content="{{ config('seo.name') }}">
    <meta name="robots" content="{{ $robots }}">
    @unless($noindex ?? false)
        <link rel="canonical" href="{{ $canonical }}">
    @endunless
    @if($alternates)
        <link rel="alternate" hreflang="nl" href="{{ $alternates['nl'] }}">
        <link rel="alternate" hreflang="en" href="{{ $alternates['en'] }}">
        <link rel="alternate" hreflang="x-default" href="{{ $alternates['nl'] }}">
    @endif
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preload" href="{{ asset('fonts/instrument-sans-latin-wght.woff2') }}" as="font" type="font/woff2" crossorigin>
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ config('seo.site_name') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="{{ $locale === 'en' ? 'en_US' : 'nl_NL' }}">
    <meta property="og:locale:alternate" content="{{ $locale === 'en' ? 'nl_NL' : 'en_US' }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ __('seo.og.image_alt', ['title' => $pageTitle]) }}">
    @if(($ogType ?? null) === 'profile')
        <meta property="profile:first_name" content="{{ config('seo.given_name') }}">
        <meta property="profile:last_name" content="{{ config('seo.family_name') }}">
    @endif
    @isset($published)
        <meta property="article:published_time" content="{{ $published }}">
        <meta property="article:modified_time" content="{{ $modified ?? $published }}">
        <meta property="article:author" content="{{ \App\Support\Seo::home() }}">
    @endisset
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
    @if($graph)
        <script type="application/ld+json">{!! \App\Support\Seo::json($graph) !!}</script>
    @endif
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

@if($isDraft)
    <div class="draft-banner" role="status">
        <div class="container">
            <strong>{{ __('site.preview.title') }}</strong>
            {{ __('site.preview.text') }}
            @if(count($draft))
                <span class="draft-banner-count">{{ trans_choice('site.preview.markers', count($draft), ['count' => count($draft)]) }}</span>
            @endif
        </div>
    </div>
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
