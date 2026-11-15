{{-- Playground: its own tinted "room" with Ask Amir, ideas, the terminal and the ping experiment. --}}
<section id="play" class="section section-band" aria-labelledby="play-title">
    <div class="container">
        <x-opener id="play-title" :label="__('site.play.label')" :lead="__('site.play.lead')">{{ __('site.play.title') }}</x-opener>

        {{-- Ask Amir --}}
        <div class="play-block read" aria-labelledby="ask-title">
            <p class="label" data-reveal>{{ __('site.ask.label') }}</p>
            <h3 id="ask-title" class="text-title mt-3 font-semibold" data-reveal>{{ __('site.ask.title') }}</h3>
            <p class="mt-3 text-fg-2" data-reveal>{{ __('site.ask.lead') }}</p>
            <div class="mt-10" data-reveal>@include('partials.ask-form')</div>
            @if($questions->isNotEmpty())
                <div class="rule-list mt-stack">
                    @foreach($questions as $question)
                        <x-question-item :question="$question" :level="4" />
                    @endforeach
                </div>
                <p class="mt-6"><a href="{{ lroute('ask') }}" class="link-arrow"><span>{{ __('site.ask.all') }}</span>{{ icon('arrow-right') }}</a></p>
            @endif
        </div>

        {{-- Suggest something --}}
        <div class="play-block read" aria-labelledby="ideas-title">
            <p class="label" data-reveal>{{ __('site.ideas.label') }}</p>
            <h3 id="ideas-title" class="text-title mt-3 font-semibold" data-reveal>{{ __('site.ideas.title') }}</h3>
            <p class="mt-3 text-fg-2" data-reveal>{{ __('site.ideas.lead') }}</p>
            <div class="mt-10" data-reveal>@include('partials.idea-form')</div>
            @if($ideas->isNotEmpty())
                <ul class="rule-list mt-stack" data-reveal-group>
                    @foreach($ideas as $idea)
                        <x-idea-row :idea="$idea" :voted="in_array($idea->id, $voted, true)" :level="4" />
                    @endforeach
                </ul>
            @endif
            <p class="mt-6"><a href="{{ lroute('roadmap') }}" class="link-arrow"><span>{{ __('site.ideas.roadmap') }}</span>{{ icon('arrow-right') }}</a></p>
        </div>

        {{-- Terminal --}}
        <div class="play-block read" aria-labelledby="term-title">
            <p class="label" data-reveal>{{ __('site.terminal.label') }}</p>
            <h3 id="term-title" class="text-title mt-3 font-semibold" data-reveal>{{ __('site.terminal.title') }}</h3>
            <p class="mt-3 text-fg-2" data-reveal>{{ __('site.terminal.lead') }}</p>
            <div class="mt-10" data-reveal><x-terminal id="term-home" /></div>
        </div>

        {{-- Ping --}}
        <div class="play-block read" aria-labelledby="ping-title" data-ping data-ping-url="{{ route('ping') }}">
            <p class="label" data-reveal>{{ __('site.ping.label') }}</p>
            <h3 id="ping-title" class="text-title mt-3 font-semibold" data-reveal>{{ __('site.ping.title') }}</h3>
            <p class="mt-3 text-fg-2" data-reveal>{{ __('site.ping.lead') }}</p>

            <div class="ping-you mt-10" data-reveal>
                <p class="text-small text-muted">{{ __('site.ping.you') }}</p>
                <p class="mt-2 text-statement font-semibold leading-none tabular" aria-live="polite"><span data-ping-value>—</span> <span class="text-h3 text-muted">ms</span></p>
                <p class="mt-2 text-small text-muted" data-ping-note data-measuring="{{ __('site.ping.measuring') }}" data-median="{{ __('site.ping.median', ['n' => ':n']) }}">{{ __('site.ping.measuring') }}</p>
                <button type="button" class="btn-secondary btn-sm mt-5" data-ping-again>{{ __('site.ping.again') }}</button>
            </div>

            <h4 class="label mt-stack" data-reveal>{{ __('site.ping.sites') }}</h4>
            @if($pingSites->isEmpty())
                <p class="mt-4 text-small text-muted">{{ __('site.ping.empty') }}</p>
            @else
                @php($max = max(1, $pingSites->max('ms')))
                <ul class="rule-list mt-4" data-reveal-group>
                    @foreach($pingSites as $site)
                        <li class="ping-row" data-reveal>
                            <span class="min-w-0"><span class="block font-medium">{{ $site['label'] }}</span><span class="block text-small text-muted">{{ $site['host'] }} · {{ $site['at']->diffForHumans() }}</span></span>
                            <span class="ping-bar" aria-hidden="true"><span style="--w: {{ $site['ok'] && $site['ms'] ? round($site['ms'] / $max * 100) : 0 }}%"></span></span>
                            <span class="tabular text-right">{{ $site['ok'] && $site['ms'] !== null ? $site['ms'].' ms' : __('site.ping.down') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-8 flex flex-wrap items-center gap-x-8 gap-y-4">
                <a href="{{ lroute('status') }}" class="link-arrow"><span>{{ __('site.ping.status') }}</span>{{ icon('arrow-right') }}</a>
                @if($statusUrl = config('site.serverscope.status_url'))
                    <a href="{{ $statusUrl }}" class="inline-flex items-center gap-3 text-small text-muted" target="_blank" rel="noopener">
                        {{ __('site.ping.monitored') }}
                        <img data-badge="{{ rtrim($statusUrl, '/') }}/badge.svg?lang={{ app()->getLocale() }}&theme=" src="{{ rtrim($statusUrl, '/') }}/badge.svg?lang={{ app()->getLocale() }}&theme=light" alt="ServerScope status" height="20" class="h-5 w-auto" loading="lazy">
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
