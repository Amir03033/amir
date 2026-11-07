@props(['d'])

{{-- ServerScope-style overview of my live sites. Every number comes from `php artisan site:check`; missing data shows "—". --}}
@php
    $score = $d['score'];
    $state = $score === null ? 'none' : ($score === 100 ? 'good' : ($score >= 50 ? 'warn' : 'bad'));
    $ring = ['good' => 'text-scope-green', 'warn' => 'text-scope-yellow', 'bad' => 'text-scope-red', 'none' => 'text-ink-strong'][$state];
    $circ = 2 * M_PI * 52;
    $glance = $d['sites']->take(8);
@endphp

<div class="space-y-4">
    {{-- Health --}}
    <div data-reveal class="panel panel-glow spotlight flex flex-col gap-6 overflow-hidden p-6 sm:flex-row sm:items-center sm:gap-10 md:p-8">
        <div class="relative h-[132px] w-[132px] shrink-0">
            <div class="absolute inset-3 rounded-full blur-2xl {{ ['good' => 'bg-scope-green/20', 'warn' => 'bg-scope-yellow/20', 'bad' => 'bg-scope-red/20', 'none' => ''][$state] }}" aria-hidden="true"></div>
            <svg viewBox="0 0 120 120" class="relative h-full w-full -rotate-90" aria-hidden="true">
                <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="9" class="text-ink-line" />
                <circle data-ring="{{ $score ?? 0 }}" cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="9" stroke-linecap="round"
                        stroke-dasharray="{{ round($circ, 2) }}" stroke-dashoffset="{{ round($circ * (1 - ($score ?? 0) / 100), 2) }}"
                        class="{{ $ring }}" />
            </svg>
            <div class="absolute inset-0 grid place-items-center text-center">
                <div>
                    <p class="text-[40px] font-bold leading-none tracking-tight tabular-nums">
                        @if($score !== null)<span data-count="{{ $score }}">{{ $score }}</span>@else—@endif
                    </p>
                    <p class="mt-1 text-xs text-ink-muted">/100</p>
                </div>
            </div>
        </div>

        <div class="min-w-0 flex-1">
            <p class="label">{{ __('messages.health.title') }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight md:text-4xl">{{ __('messages.health.state.'.$state) }}</p>
            <p class="mt-2 text-sm text-ink-soft">
                {{ trans_choice('messages.health.sites', $d['total'], ['up' => $d['up'], 'total' => $d['total']]) }}
                · {{ __('messages.health.incidents', ['count' => $d['incidents']]) }}
                @if($d['uptime_7d'] !== null)
                    · {{ __('messages.health.uptime', ['pct' => rtrim(rtrim(number_format($d['uptime_7d'], 2), '0'), '.')]) }}
                @endif
            </p>
        </div>

    </div>

    {{-- Everything at a glance --}}
    <div data-reveal class="panel p-5 md:p-6">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <p class="label">{{ __('messages.health.glance') }}</p>
            <p class="text-[11px] text-ink-muted">{{ __('messages.health.measured') }}</p>
        </div>
        @if($glance->isNotEmpty())
            <ul class="mt-4 grid gap-2.5 sm:grid-cols-2 lg:grid-cols-4" data-glance>
                @foreach($glance as $site)
                    @php($ok = $site['latest']->ok)
                    <li class="flex items-center justify-between gap-3 rounded-xl border border-ink-line bg-ink-panel2 px-3.5 py-2.5 transition-colors hover:border-ink-strong">
                        <span class="flex min-w-0 items-center gap-2.5">
                            <span class="dot {{ $ok ? 'bg-scope-green' : 'bg-scope-red' }}" aria-label="{{ $ok ? 'up' : 'down' }}"></span>
                            <span class="truncate text-sm font-semibold">{{ $site['host'] }}</span>
                        </span>
                        <span class="shrink-0 text-sm tabular-nums text-ink-muted">
                            @if($site['latest']->response_ms !== null)<span data-count="{{ $site['latest']->response_ms }}">{{ $site['latest']->response_ms }}</span> ms @else — @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-4 text-sm text-ink-muted">{{ __('messages.status.empty') }}</p>
        @endif
    </div>

    <div class="grid gap-4 lg:grid-cols-[1.4fr_1fr]">
        {{-- Last 24 hours --}}
        <div data-reveal class="panel p-5 md:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="label">{{ __('messages.health.last24') }}</p>
                <p class="flex items-center gap-3 text-[11px] text-ink-muted">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-scope-green"></span>{{ __('messages.health.legend_ok') }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-scope-red"></span>{{ __('messages.health.legend_fail') }}</span>
                </p>
            </div>
            <div class="mt-4 grid grid-cols-[repeat(24,minmax(0,1fr))] overflow-hidden rounded-xl border border-ink-line" data-timeline>
                @foreach($d['hours'] as $hour)
                    <span title="{{ $hour['at']->format('H:i') }} · {{ $hour['count'] }} checks"
                          class="h-10 border-r border-ink-line last:border-r-0 {{ $hour['state'] === null ? 'bg-ink-panel2' : ($hour['state'] ? 'bg-scope-green/70' : 'bg-scope-red/80') }}"></span>
                @endforeach
            </div>
            <div class="mt-2 flex justify-between text-[11px] tabular-nums text-ink-muted">
                @foreach([0, 6, 12, 18, 23] as $i)
                    <span>{{ $d['hours'][$i]['at']->format('H:i') }}</span>
                @endforeach
            </div>
        </div>

        {{-- Heartbeat --}}
        <div data-reveal class="panel flex flex-col p-5 md:p-6">
            <div class="flex items-center justify-between">
                <p class="label">{{ __('messages.health.heartbeat') }}</p>
                <span class="flex items-center gap-2 text-xs font-semibold {{ $state === 'good' ? 'text-scope-green' : 'text-scope-yellow' }}">
                    <span class="dot {{ $state === 'good' ? 'bg-scope-green' : 'bg-scope-yellow' }}"></span>{{ __('messages.health.beat.'.($state === 'good' ? 'steady' : 'unstable')) }}
                </span>
            </div>
            <canvas data-heartbeat="{{ $state === 'good' ? 'ok' : 'warn' }}" class="mt-4 h-12 w-full" aria-hidden="true"></canvas>
            <dl class="mt-4 grid grid-cols-2 gap-4 border-t border-ink-line pt-4">
                <div>
                    <dt class="text-[11px] text-ink-muted">{{ __('messages.health.avg') }}</dt>
                    <dd class="mt-0.5 text-lg font-bold tabular-nums">@if($d['avg_ms'] !== null)<span data-count="{{ $d['avg_ms'] }}">{{ $d['avg_ms'] }}</span> ms @else — @endif</dd>
                </div>
                <div>
                    <dt class="text-[11px] text-ink-muted">{{ __('messages.health.slowest') }}</dt>
                    <dd class="mt-0.5 text-lg font-bold tabular-nums">@if($d['slowest'])<span data-count="{{ $d['slowest']['ms'] }}">{{ $d['slowest']['ms'] }}</span> ms @else — @endif</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
