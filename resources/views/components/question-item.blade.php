@props(['question', 'level' => 3])
<article class="py-8" data-reveal>
    <h{{ $level }} class="text-h3 font-semibold">{{ $question->body }}</h{{ $level }}>
    <p class="mt-1 text-small text-muted">{{ $question->name ?: __('site.ask.anonymous') }} · <time datetime="{{ $question->answered_at->toDateString() }}">{{ $question->answered_at->translatedFormat('j M Y') }}</time></p>
    <div class="mt-4 whitespace-pre-line text-fg-2">{{ $question->answer }}</div>
    <p class="mt-3 text-small text-muted">{{ __('site.ask.by') }}</p>
</article>
