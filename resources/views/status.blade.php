@extends('layouts.layout', [
    'title' => __('messages.status.page_title').' — Amir Jebbari',
    'description' => __('messages.status.page_intro'),
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('messages.status.page_title') }}</p>
        <h1 class="statement mx-auto mt-5 max-w-[16ch]" data-hero-line style="--i: 1">
            {{ $sites->isEmpty() ? __('messages.health.state.none') : ($down ? trans_choice('messages.status.banner_down', $down, ['count' => $down]) : __('messages.status.banner_ok')) }}
        </h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('messages.status.page_intro') }}</p>
        <p class="mt-4 text-small text-muted" data-hero-line style="--i: 3">{{ __('messages.hero.last_check') }}: {{ $lastCheck?->diffForHumans() ?? '—' }}</p>
    </header>

    <section class="container pb-section">
        <div class="read">
            @forelse($sites as $site)
                @php($ok = $site['latest']?->ok)
                <section class="border-t border-line py-10" data-reveal>
                    <div class="flex flex-wrap items-baseline justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="text-h3 font-semibold">
                                <span class="mr-2 inline-block h-2 w-2 rounded-full align-middle {{ $ok ? 'bg-ok' : 'bg-err' }}" aria-hidden="true"></span>{{ $site['label'] }}
                                <span class="sr-only">{{ $ok ? __('messages.health.legend_ok') : __('messages.health.legend_fail') }}</span>
                            </h2>
                            <a href="{{ $site['url'] }}" target="_blank" rel="noopener noreferrer" class="link mt-1 inline-block text-small text-muted">{{ $site['host'] }}</a>
                        </div>
                        <dl class="flex gap-8 text-right">
                            <div><dt class="label">{{ __('messages.status.uptime_30') }}</dt><dd class="mt-1 font-semibold tabular">{{ $site['uptime'] !== null ? rtrim(rtrim(number_format($site['uptime'], 2), '0'), '.').'%' : '—' }}</dd></div>
                            <div><dt class="label">{{ __('messages.health.avg') }}</dt><dd class="mt-1 font-semibold tabular">{{ $site['avg_ms'] ? $site['avg_ms'].' ms' : '—' }}</dd></div>
                        </dl>
                    </div>

                    <x-uptime-bar :days="$site['days']" class="mt-6" />

                    @if($site['outages']->isNotEmpty())
                        <details class="mt-6 text-small">
                            <summary class="cursor-pointer text-fg-2 hover:text-fg">{{ trans_choice('messages.status.outages', $site['outages']->count(), ['count' => $site['outages']->count()]) }}</summary>
                            <ul class="rule-list mt-4 text-muted">
                                @foreach($site['outages'] as $o)
                                    <li class="flex flex-wrap justify-between gap-2 py-3">
                                        <span>{{ $o['start']->translatedFormat('j M, H:i') }} → {{ $o['end']?->translatedFormat('H:i') ?? __('messages.status.ongoing') }}</span>
                                        <span class="tabular">{{ $o['minutes'] }} min @if($o['code']) · HTTP {{ $o['code'] }} @endif</span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                </section>
            @empty
                <p class="empty">{{ __('messages.status.empty') }}</p>
            @endforelse
            <p class="border-t border-line pt-6 text-small text-muted">{{ __('messages.status.legend_page') }}</p>
        </div>
    </section>
@endsection
