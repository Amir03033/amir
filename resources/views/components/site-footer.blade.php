@php
    $email = config('site.email');
    $linkedin = config('site.linkedin');
    $github = config('site.github');
@endphp

<footer class="border-t border-slate-800/80 py-10 text-center text-xs font-mono text-slate-500 relative z-10">
    <div class="flex justify-center gap-5 mb-4 text-slate-400">
        <a href="mailto:{{ $email }}" class="hover:text-cyan-400 transition">E-mail</a>
        <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-400 transition">LinkedIn</a>
        @if($github)
            <a href="{{ $github }}" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-400 transition">GitHub</a>
        @endif
        <a href="{{ route('cv') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.cv') }}</a>
    </div>
    <p>&copy; {{ date('Y') }} {{ config('site.name') }}. {{ __('messages.footer.line') }}</p>
</footer>
