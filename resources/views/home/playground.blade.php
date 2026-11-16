{{-- Playground: a tinted band with four small cards (Ask Amir, ideas, terminal, ping). --}}
<section id="play" class="section section-band" aria-labelledby="play-title">
    <div class="container">
        <x-opener id="play-title" :label="__('site.play.label')" :lead="__('site.play.lead')">{{ __('site.play.title') }}</x-opener>

        {{-- Four small cards instead of four long sections; the forms live on their own pages. --}}
        <div class="play-grid mt-stack" data-reveal-group>
            {{-- Ask Amir: the latest answer, and a link to ask (form on /ask). --}}
            <article class="play-card" aria-labelledby="ask-title" data-reveal>
                <p class="label">{{ __('site.ask.label') }}</p>
                <h3 id="ask-title" class="play-card-title">{{ __('site.ask.title') }}</h3>
                <p class="mt-2 text-small text-fg-2">{{ __('site.ask.lead') }}</p>
                @if($questions->isNotEmpty())
                    <div class="rule-list mt-6">
                        <x-question-item :question="$questions->first()" :level="4" />
                    </div>
                @endif
                <p class="play-card-actions">
                    <a href="{{ lroute('ask') }}#ask-form" class="btn-primary btn-sm">{{ __('site.ask.send') }}</a>
                    @if($questions->isNotEmpty())<a href="{{ lroute('ask') }}" class="link-arrow text-small"><span>{{ __('site.ask.all') }}</span>{{ icon('arrow-right') }}</a>@endif
                </p>
            </article>

            {{-- Ideas: the top three to vote on, and a link to suggest one (form on /roadmap). --}}
            <article class="play-card" aria-labelledby="ideas-title" data-reveal>
                <p class="label">{{ __('site.ideas.label') }}</p>
                <h3 id="ideas-title" class="play-card-title">{{ __('site.ideas.title') }}</h3>
                <p class="mt-2 text-small text-fg-2">{{ __('site.ideas.lead') }}</p>
                @if($ideas->isNotEmpty())
                    <ul class="rule-list mt-6">
                        @foreach($ideas->take(3) as $idea)
                            <x-idea-row :idea="$idea" :voted="in_array($idea->id, $voted, true)" :level="4" />
                        @endforeach
                    </ul>
                @endif
                <p class="play-card-actions">
                    <a href="{{ lroute('roadmap') }}#idea-form" class="btn-primary btn-sm">{{ __('site.ideas.send') }}</a>
                    <a href="{{ lroute('roadmap') }}" class="link-arrow text-small"><span>{{ __('site.ideas.roadmap') }}</span>{{ icon('arrow-right') }}</a>
                </p>
            </article>

        {{-- Terminal --}}
        <article class="play-card" aria-labelledby="term-title" data-reveal>
            <p class="label">{{ __('site.terminal.label') }}</p>
            <h3 id="term-title" class="play-card-title">{{ __('site.terminal.title') }}</h3>
            <p class="mt-2 text-small text-fg-2">{{ __('site.terminal.lead') }}</p>
            <div class="mt-6"><x-terminal id="term-home" /></div>
        </article>

        {{-- Ping --}}
        <article class="play-card" data-reveal aria-labelledby="ping-title" data-ping data-ping-url="{{ route('ping') }}">
            <p class="label">{{ __('site.ping.label') }}</p>
            <h3 id="ping-title" class="play-card-title">{{ __('site.ping.title') }}</h3>
            <p class="mt-2 text-small text-fg-2">{{ __('site.ping.lead') }}</p>

            <div class="ping-you mt-6">
                <p class="text-small text-muted">{{ __('site.ping.you') }}</p>
                <p class="mt-1 text-title font-semibold leading-none tabular" aria-live="polite"><span data-ping-value>—</span> <span class="text-h3 text-muted">ms</span></p>
                <p class="mt-2 text-small text-muted" data-ping-note data-measuring="{{ __('site.ping.measuring') }}" data-median="{{ __('site.ping.median', ['n' => ':n']) }}">{{ __('site.ping.measuring') }}</p>
                <button type="button" class="btn-secondary btn-sm mt-4" data-ping-again>{{ __('site.ping.again') }}</button>
            </div>

            <h4 class="label mt-8">{{ __('site.ping.sites') }}</h4>
            @if($pingSites->isEmpty())
                <p class="mt-4 text-small text-muted">{{ __('site.ping.empty') }}</p>
            @else
                @php($max = max(1, $pingSites->max('ms')))
                <ul class="rule-list mt-3">
                    @foreach($pingSites as $site)
                        <li class="ping-row">
                            <span class="min-w-0"><span class="block font-medium">{{ $site['label'] }}</span><span class="block text-small text-muted">{{ $site['host'] }} · {{ $site['at']->diffForHumans() }}</span></span>
                            <span class="ping-bar" aria-hidden="true"><span style="--w: {{ $site['ok'] && $site['ms'] ? round($site['ms'] / $max * 100) : 0 }}%"></span></span>
                            <span class="tabular text-right">{{ $site['ok'] && $site['ms'] !== null ? $site['ms'].' ms' : __('site.ping.down') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="play-card-actions">
                <a href="{{ lroute('status') }}" class="link-arrow"><span>{{ __('site.ping.status') }}</span>{{ icon('arrow-right') }}</a>
                @if($statusUrl = config('site.serverscope.status_url'))
                    <a href="{{ $statusUrl }}" class="inline-flex items-center gap-3 text-small text-muted" target="_blank" rel="noopener">
                        {{ __('site.ping.monitored') }}
                        <img data-badge="{{ rtrim($statusUrl, '/') }}/badge.svg?lang={{ app()->getLocale() }}&theme=" src="{{ rtrim($statusUrl, '/') }}/badge.svg?lang={{ app()->getLocale() }}&theme=light" alt="ServerScope status" height="20" class="h-5 w-auto" loading="lazy">
                    </a>
                @endif
            </div>
        </article>
        </div>
    </div>
</section>
