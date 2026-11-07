@props(['blogs'])

<div class="panel divide-y divide-ink-line overflow-hidden" data-reveal-group>
    @foreach($blogs as $blog)
        <a data-reveal href="{{ lroute('blog.show', $blog->slug) }}" class="group grid gap-2 px-5 py-5 transition-colors hover:bg-ink-panel2 md:grid-cols-[120px_1fr_auto] md:items-center md:gap-6">
            <span class="text-xs tabular-nums text-ink-muted">
                <time datetime="{{ $blog->publishedDate()->toDateString() }}">{{ $blog->publishedDate()->format('Y-m-d') }}</time>
                <span class="block">{{ __('messages.blog.minutes', ['n' => $blog->readingMinutes()]) }}</span>
            </span>
            <span>
                <span class="block font-semibold transition-colors group-hover:text-scope-accent">{{ $blog->getTranslation('title') }}</span>
                <span class="mt-1 block text-sm leading-relaxed text-ink-muted">{{ $blog->excerpt() }}</span>
                @if($blog->tagList())
                    <span class="mt-2 flex flex-wrap gap-1.5">
                        @foreach(array_slice($blog->tagList(), 0, 4) as $tag)<span class="chip">{{ $tag }}</span>@endforeach
                    </span>
                @endif
            </span>
            <span class="text-xs font-semibold text-ink-muted transition-[color,transform] group-hover:translate-x-1 group-hover:text-scope-accent">{{ __('messages.blog.read') }} →</span>
        </a>
    @endforeach
</div>
