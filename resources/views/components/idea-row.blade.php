@props(['idea', 'voted' => false])
{{-- One idea on the roadmap. The vote button posts to /ideas/{id}/vote (one vote per visitor). --}}
<li class="idea-row" data-reveal
    x-data="{ votes: {{ $idea->votes_count }}, voted: {{ $voted ? 'true' : 'false' }}, busy: false,
        async vote() {
            if (this.voted || this.busy) return;
            this.busy = true;
            try {
                const r = await fetch(@js(route('ideas.vote', $idea)), { method: 'POST', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } });
                const d = await r.json().catch(() => ({}));
                if (r.ok) { this.votes = d.votes; this.voted = true; window.toast?.(d.message); }
                else window.toast?.(r.status === 429 ? @js(__('messages.form.error_throttle')) : @js(__('messages.form.error_generic')), { type: 'error' });
            } finally { this.busy = false; }
        } }">
    <div class="min-w-0">
        <h3 class="font-semibold">{{ $idea->title }}</h3>
        @if($idea->body)<p class="mt-1 text-small text-muted">{{ $idea->body }}</p>@endif
    </div>
    @if($idea->isVotable())
        <button type="button" class="vote" @click="vote" :aria-pressed="voted.toString()" aria-pressed="{{ $voted ? 'true' : 'false' }}" :disabled="busy"
                aria-label="{{ __('site.ideas.vote') }}: {{ $idea->title }}">
            {{ icon('chevron-up') }}<span class="tabular" x-text="votes">{{ $idea->votes_count }}</span>
        </button>
    @else
        <span class="text-small text-muted tabular">{{ trans_choice('site.ideas.votes', $idea->votes_count, ['count' => $idea->votes_count]) }}</span>
    @endif
</li>
