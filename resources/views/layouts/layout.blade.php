<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amir Jebbari | Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { cyber: { bg: '#030712', card: '#0b0f19', accent: '#06b6d4' } }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body::after {
            content: " "; display: block; position: fixed; inset: 0;
            background: linear-gradient(rgba(18,16,16,0) 50%, rgba(0,0,0,0.15) 50%), linear-gradient(90deg, rgba(255,0,0,0.03), rgba(0,255,0,0.01), rgba(0,0,255,0.03));
            z-index: 9999; pointer-events: none; background-size: 100% 4px, 6px 100%; opacity: 0.4;
        }
    </style>
</head>
<body
        x-data="{
        locale: '{{ app()->getLocale() }}',
        resizing: false,
        messages: {{ json_encode(trans('messages')) }},
        async switchLanguage(newLocale) {
            this.resizing = true;
            let response = await fetch('{{ route('lang.switch') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ locale: newLocale })
            });
            let data = await response.json();
            if(data.success) { this.locale = data.locale; this.messages = data.messages; }
            setTimeout(() => this.resizing = false, 200);
        }
    }"
        class="bg-cyber-bg text-slate-100 font-sans antialiased overflow-x-hidden selection:bg-cyan-500 selection:text-black"
>
<!-- Background grid layer -->
<div class="fixed inset-0 bg-[linear-gradient(to_right,#1f293710_1px,transparent_1px),linear-gradient(to_bottom,#1f293710_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none z-0"></div>

@yield('nav')

<main :class="resizing ? 'opacity-30' : 'opacity-100'" class="transition-opacity duration-300 relative z-10">
    @yield('content')
</main>

<footer class="border-t border-slate-900/60 py-8 text-center text-xs font-mono text-slate-600">
    <p>&copy; 2026 Amir Jebbari. Built with High-End Tech & Precision.</p>
</footer>
</body>
</html>