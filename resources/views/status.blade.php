@extends('layouts.layout', [
    'title' => __('messages.status.page_title').' — '.config('site.name'),
    'description' => __('messages.status.page_intro'),
])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <div class="px-4 pb-24 pt-28 md:pt-36">
        <div class="mx-auto max-w-4xl">
            <nav data-reveal class="flex items-center gap-2 text-sm text-ink-muted" aria-label="Breadcrumb">
                <a href="{{ lroute('home') }}" class="hover:text-ink-text">{{ __('messages.hero.crumb') }}</a>
                <span>›</span>
                <span class="text-ink-soft">{{ __('messages.status.page_title') }}</span>
            </nav>

            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 data-split class="text-4xl font-bold tracking-[-.03em] md:text-6xl">{{ __('messages.status.page_title') }}</h1>
                    <p data-reveal class="mt-4 max-w-xl text-ink-soft">{{ __('messages.status.page_intro') }}</p>
                </div>
                <p data-reveal class="text-xs text-ink-muted">{{ __('messages.hero.last_check') }}: <span class="font-semibold text-ink-text">{{ $lastCheck?->diffForHumans() ?? '—' }}</span></p>
            </div>

            {{-- Overall banner --}}
            <div data-reveal class="panel panel-glow mt-10 flex items-center gap-4 p-5 md:p-6">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full {{ $sites->isEmpty() ? 'bg-ink-line text-ink-muted' : ($down ? 'bg-scope-red/15 text-scope-red' : 'bg-scope-green/15 text-scope-green') }}">
                    @if($down)
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 8v5M12 16.5v.5"/><circle cx="12" cy="12" r="9"/></svg>
                    @else
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m8 12 3 3 5-6"/><circle cx="12" cy="12" r="9"/></svg>
                    @endif
                </span>
                <div>
                    <p class="text-xl font-bold tracking-tight">
                        {{ $sites->isEmpty() ? __('messages.health.state.none') : ($down ? trans_choice('messages.status.banner_down', $down, ['count' => $down]) : __('messages.status.banner_ok')) }}
                    </p>
                    <p class="text-sm text-ink-muted">{{ __('messages.health.measured') }}</p>
                </div>
            </div>

            <div class="mt-6 space-y-4" data-reveal-group>
                @forelse($sites as $site)
                    @php($ok = $site['latest']?->ok)
                    <section data-reveal class="panel p-5 md:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="flex items-center gap-2.5 font-semibold">
                                    <span class="dot {{ $ok ? 'bg-scope-green' : 'bg-scope-red' }}"></span>{{ $site['label'] }}
                                </h2>
                                <a href="{{ $site['url'] }}" target="_blank" rel="noopener noreferrer" class="mt-0.5 block truncate pl-[18px] text-xs text-ink-muted hover:text-scope-accent">{{ $site['host'] }} ↗</a>
                            </div>
                            <dl class="flex gap-6 text-right">
                                <div>
                                    <dt class="text-[11px] text-ink-muted">{{ __('messages.status.uptime_30') }}</dt>
                                    <dd class="font-bold tabular-nums">{{ $site['uptime'] !== null ? rtrim(rtrim(number_format($site['uptime'], 2), '0'), '.').'%' : '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] text-ink-muted">{{ __('messages.health.avg') }}</dt>
                                    <dd class="font-bold tabular-nums">{{ $site['avg_ms'] ? $site['avg_ms'].' ms' : '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <x-uptime-bar :days="$site['days']" class="mt-5" />

                        @if($site['outages']->isNotEmpty())
                            <details class="group/o mt-4 border-t border-ink-line pt-3 text-sm">
                                <summary class="flex cursor-pointer list-none items-center gap-2 text-ink-soft hover:text-ink-text">
                                    <span class="transition-transform group-open/o:rotate-90">›</span>
                                    {{ trans_choice('messages.status.outages', $site['outages']->count(), ['count' => $site['outages']->count()]) }}
                                </summary>
                                <ul class="mt-3 space-y-1.5 text-xs text-ink-muted">
                                    @foreach($site['outages'] as $o)
                                        <li class="flex flex-wrap justify-between gap-2 rounded-lg bg-ink-panel2 px-3 py-2">
                                            <span>{{ $o['start']->translatedFormat('j M, H:i') }} → {{ $o['end']?->translatedFormat('H:i') ?? __('messages.status.ongoing') }}</span>
                                            <span class="tabular-nums">{{ $o['minutes'] }} min @if($o['code']) · HTTP {{ $o['code'] }} @endif</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </section>
                @empty
                    <p class="panel p-8 text-center text-sm text-ink-muted">{{ __('messages.status.empty') }}</p>
                @endforelse
            </div>

            <p class="mt-6 text-xs text-ink-muted">{{ __('messages.status.legend_page') }}</p>
        </div>
    </div>
@endsection
