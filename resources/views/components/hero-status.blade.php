@props(['d'])

{{-- Compact live-status card for the hero: the highlights of the "Live status" section, all from real checks. --}}
@php
    $score = $d['score'];
    $state = $score === null ? 'none' : ($score === 100 ? 'good' : ($score >= 50 ? 'warn' : 'bad'));
    $ring = ['good' => 'text-scope-green', 'warn' => 'text-scope-yellow', 'bad' => 'text-scope-red', 'none' => 'text-ink-strong'][$state];
    $glow = ['good' => 'bg-scope-green/20', 'warn' => 'bg-scope-yellow/20', 'bad' => 'bg-scope-red/20', 'none' => ''][$state];
    $circ = 2 * M_PI * 40;
    $pct = fn ($v) => rtrim(rtrim(number_format($v, 2), '0'), '.');
@endphp

<a href="#status" data-tilt class="panel panel-glow spotlight group block overflow-hidden shadow-[0_30px_80px_-40px_rgb(110_156_247/.45)] transition-colors hover:border-ink-strong" aria-label="{{ __('messages.status.section_title') }}">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-ink-line px-5 py-3">
        <span class="label">{{ __('messages.status.section_label') }}</span>
        <span class="flex items-center gap-2 text-[11px] text-ink-muted">
            <span class="dot {{ $state === 'good' ? 'bg-scope-green' : 'bg-scope-yellow' }} h-1.5 w-1.5"></span>
            {{ $d['last_check']?->diffForHumans() ?? '—' }}
        </span>
    </div>

    {{-- Health --}}
    <div class="flex items-center gap-5 px-5 pt-5">
        <div class="relative h-[92px] w-[92px] shrink-0">
            <div class="absolute inset-3 rounded-full blur-xl {{ $glow }}" aria-hidden="true"></div>
            <svg viewBox="0 0 100 100" class="relative h-full w-full -rotate-90" aria-hidden="true">
                <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="8" class="text-ink-line" />
                <circle data-ring cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"
                        stroke-dasharray="{{ round($circ, 2) }}" stroke-dashoffset="{{ round($circ * (1 - ($score ?? 0) / 100), 2) }}" class="{{ $ring }}" />
            </svg>
            <div class="absolute inset-0 grid place-items-center">
                <p class="text-2xl font-bold tabular-nums">@if($score !== null)<span data-count="{{ $score }}">{{ $score }}</span>@else—@endif</p>
            </div>
        </div>
        <div class="min-w-0">
            <p class="text-xl font-bold tracking-tight">{{ __('messages.health.state.'.$state) }}</p>
            <p class="mt-1 text-sm text-ink-muted">{{ trans_choice('messages.health.sites', $d['total'], ['up' => $d['up'], 'total' => $d['total']]) }}</p>
        </div>
    </div>

    {{-- Key numbers --}}
    <dl class="mx-5 mt-5 grid grid-cols-3 divide-x divide-ink-line rounded-xl border border-ink-line bg-ink-panel2">
        <div class="px-3 py-2.5">
            <dt class="text-[10px] uppercase tracking-[.14em] text-ink-muted">Uptime</dt>
            <dd class="mt-0.5 text-lg font-bold tabular-nums">@if($d['uptime_7d'] !== null)<span data-count="{{ $d['uptime_7d'] }}" data-decimals="{{ $d['uptime_7d'] == floor($d['uptime_7d']) ? 0 : 1 }}">{{ $pct($d['uptime_7d']) }}</span><span class="text-xs text-ink-muted">%</span>@else—@endif</dd>
        </div>
        <div class="px-3 py-2.5">
            <dt class="text-[10px] uppercase tracking-[.14em] text-ink-muted">{{ __('messages.hero.response') }}</dt>
            <dd class="mt-0.5 text-lg font-bold tabular-nums">@if($d['avg_ms'] !== null)<span data-count="{{ $d['avg_ms'] }}">{{ $d['avg_ms'] }}</span><span class="text-xs text-ink-muted"> ms</span>@else—@endif</dd>
        </div>
        <div class="px-3 py-2.5">
            <dt class="text-[10px] uppercase tracking-[.14em] text-ink-muted">{{ __('messages.hero.incidents') }}</dt>
            <dd class="mt-0.5 text-lg font-bold tabular-nums {{ $d['incidents'] ? 'text-scope-red' : '' }}">{{ $d['incidents'] }}</dd>
        </div>
    </dl>

    {{-- Heartbeat --}}
    <canvas data-heartbeat="{{ $state === 'good' ? 'ok' : 'warn' }}" class="mx-5 mt-4 block h-10 w-[calc(100%-2.5rem)]" aria-hidden="true"></canvas>

    {{-- Sites --}}
    <ul class="mt-3 space-y-1.5 px-5" data-glance>
        @foreach($d['sites']->take(3) as $site)
            <li class="flex items-center justify-between gap-3 text-sm">
                <span class="flex min-w-0 items-center gap-2.5">
                    <span class="dot h-1.5 w-1.5 {{ $site['latest']->ok ? 'bg-scope-green' : 'bg-scope-red' }}"></span>
                    <span class="truncate text-ink-soft">{{ $site['host'] }}</span>
                </span>
                <span class="shrink-0 tabular-nums text-ink-muted">{{ $site['latest']->response_ms !== null ? $site['latest']->response_ms.' ms' : '—' }}</span>
            </li>
        @endforeach
    </ul>

    {{-- Footer --}}
    <div class="mt-4 flex items-center justify-between border-t border-ink-line px-5 py-3 text-xs">
        <span class="truncate text-ink-muted">{{ __('messages.hero.measured_short') }}</span>
        <span class="shrink-0 font-semibold text-scope-accent">{{ __('messages.hero.more_stats') }} <span class="inline-block transition-transform group-hover:translate-x-1">→</span></span>
    </div>
</a>
