@props(['id' => 'term'])
{{-- The terminal toy (terminal.js). Data comes from a cached view composer (AppServiceProvider). --}}
@php
    $config = [
        'projects' => $terminalProjects ?? [],
        'now' => \App\Models\Setting::get('now.'.app()->getLocale()) ?: \App\Models\Setting::get('now.nl'),
        'about' => __('site.about.text').' '.__('site.about.text_2'),
        'email' => config('site.email'),
        'links' => [
            'home' => lroute('home'), 'work' => lroute('work'), 'notes' => lroute('blog.index'), 'roadmap' => lroute('roadmap'),
            'cv' => lroute('cv.pdf'), 'jebbari' => config('site.jebbari.url'), 'serverscope' => config('site.serverscope.url'),
            'nl' => alternate_url('nl'), 'en' => alternate_url('en'), 'linkedin' => config('site.linkedin'),
        ],
        'endpoints' => ['ask' => route('ask.store'), 'idea' => route('ideas.store'), 'ping' => route('ping')],
        't' => __('site.terminal'),
    ];
@endphp
<div class="term" data-terminal id="{{ $id }}" data-config='@json($config, JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP)'>
    <div class="term-out" data-term-out role="log" aria-live="polite" aria-label="{{ __('site.terminal.label') }}">
        <p class="term-muted">{{ __('site.terminal.welcome') }}</p>
    </div>
    <form class="term-in" data-term-form autocomplete="off">
        <label for="{{ $id }}-input" class="term-prompt" aria-hidden="true">amir@life ~ <span class="term-accent">%</span></label>
        <input id="{{ $id }}-input" type="text" class="term-input" data-term-input aria-label="{{ __('site.terminal.input') }}"
               placeholder="{{ __('site.terminal.hint') }}" autocapitalize="off" autocorrect="off" spellcheck="false" enterkeyhint="send" maxlength="300">
    </form>
    <div class="term-chips" aria-label="{{ __('site.terminal.label') }}">
        @foreach(['help', 'about', 'projects', 'now', 'ping', 'contact'] as $cmd)
            <button type="button" data-term-cmd="{{ $cmd }}">{{ $cmd }}</button>
        @endforeach
    </div>
</div>
