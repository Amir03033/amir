{{-- Standalone on purpose: inline CSS, no Vite manifest, no database. It must render when the app is broken. --}}
@php
    $locale = request()->segment(1) === 'en' ? 'en' : 'nl';
    $t = fn (string $key) => __('messages.errors.'.$key, [], $locale);
    $home = $locale === 'en' ? url('/en') : url('/');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $t('server') }} — amirs.life</title>
    <style>
        @font-face { font-family: 'Instrument Sans'; font-weight: 400 700; font-display: swap; src: url('/fonts/instrument-sans-latin-wght.woff2') format('woff2'); }
        :root { --bg: #F7F5F1; --text: #161512; --muted: #6B655B; --line: #CFC8BC; --accent: #7F1D3F; color-scheme: light dark; }
        @media (prefers-color-scheme: dark) { :root { --bg: #121110; --text: #F2EFE9; --muted: #A39E95; --line: #45413B; --accent: #D8799A; } }
        * { box-sizing: border-box; margin: 0; }
        body { min-height: 100svh; display: grid; place-items: center; padding: 24px; background: var(--bg); color: var(--text); font: 400 1.0625rem/1.6 'Instrument Sans', Arial, sans-serif; text-align: center; -webkit-font-smoothing: antialiased; }
        .mark { font-weight: 600; letter-spacing: -.02em; } .mark span { color: var(--accent); }
        h1 { margin-top: 40px; font-size: clamp(2.25rem, 1.4rem + 3.5vw, 4.5rem); line-height: 1.04; letter-spacing: -.025em; font-weight: 600; max-width: 16ch; margin-inline: auto; text-wrap: balance; }
        p { margin-top: 20px; color: var(--muted); max-width: 28rem; margin-inline: auto; }
        .actions { margin-top: 36px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        a, button { display: inline-flex; align-items: center; min-height: 48px; padding: 0 24px; border-radius: 999px; font: 500 .9375rem/1 inherit; text-decoration: none; cursor: pointer; }
        .primary { background: var(--accent); color: #fff; border: 1px solid transparent; }
        @media (prefers-color-scheme: dark) { .primary { color: #121110; } }
        .secondary { background: none; color: var(--text); border: 1px solid var(--line); }
        :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
    </style>
</head>
<body>
<main>
    <p class="mark" style="margin:0;color:var(--text);display:inline-flex;align-items:center;gap:10px"><svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="8.5" fill="none" stroke="var(--accent)" stroke-width="1.25"/><circle cx="10" cy="10" r="2" fill="var(--accent)"/></svg>amirs.life</p>
    <h1>{{ $t('server') }}</h1>
    <p>{{ $t('server_text') }}</p>
    <div class="actions">
        <button type="button" class="primary" onclick="location.reload()">{{ $t('retry') }}</button>
        <a class="secondary" href="{{ $home }}">{{ $t('home') }}</a>
    </div>
</main>
</body>
</html>
