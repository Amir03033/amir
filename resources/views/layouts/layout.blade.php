<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('site.name').' | Portfolio' }}</title>
    <meta name="description" content="{{ $description ?? __('messages.home.text') }}">
    <meta name="author" content="{{ config('site.name') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('site.name').' | Portfolio' }}">
    <meta property="og:description" content="{{ $description ?? __('messages.home.text') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset(config('site.photo')) }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{
        locale: '{{ app()->getLocale() }}',
        mobileOpen: false,
        async switchLanguage(newLocale) {
            await fetch('{{ route('lang.switch') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ locale: newLocale })
            });
            window.location.reload();
        }
    }"
    class="bg-cyber-bg text-slate-100 font-sans antialiased overflow-x-hidden selection:bg-cyan-500 selection:text-black"
>
<div class="fixed inset-0 bg-[linear-gradient(to_right,#1f293710_1px,transparent_1px),linear-gradient(to_bottom,#1f293710_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none z-0"></div>

@yield('nav')

<main class="relative z-10">
    @yield('content')
</main>

<x-site-footer />
</body>
</html>
