@props(['blog'])
<li data-reveal>
    <a href="{{ lroute('blog.show', $blog->slug) }}" class="group grid grid-cols-[1fr_auto] items-baseline gap-4 py-6 md:grid-cols-[9.5rem_1fr_auto]">
        <time class="label tabular hidden whitespace-nowrap md:block" datetime="{{ $blog->publishedDate()->toDateString() }}">{{ $blog->publishedDate()->translatedFormat('j M Y') }}</time>
        <span class="min-w-0">
            <span class="block text-h3 font-semibold decoration-line-strong decoration-1 underline-offset-[.2em] group-hover:underline">{{ $blog->getTranslation('title') }}</span>
            <span class="mt-1 block text-small text-muted md:hidden">{{ $blog->publishedDate()->translatedFormat('j M Y') }}</span>
        </span>
        <span class="text-small text-muted tabular">{{ __('site.notes.minutes', ['n' => $blog->readingMinutes()]) }}</span>
    </a>
</li>
