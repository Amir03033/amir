@props(['project', 'index' => 1])
{{-- One featured project as a pinned, scroll-driven chapter (DESIGN.md §5 "Work chapter"). --}}
@php
    $meta = collect([$project->year, $project->getTranslation('role') ?: null])->filter()->implode(' — ');
@endphp
<article class="pin chapter" aria-labelledby="chapter-{{ $project->id }}">
    <div class="pin-stage">
        <div class="container chapter-inner" {{ scrub('fade-out', 86, 100) }}>
            @if($meta)<p class="text-small text-muted" {{ scrub('fade-up', 0, 18) }}>{{ $meta }}</p>@endif
            <h3 id="chapter-{{ $project->id }}" class="statement mx-auto mt-4 max-w-[16ch]" {{ scrub('fade-up', 3, 24) }}>{{ $project->getTranslation('title') }}</h3>
            <p class="lead mx-auto mt-4 max-w-xl" {{ scrub('fade-up', 6, 28) }}>{{ $project->oneLiner() }}</p>
            <a href="{{ lroute('project.show', $project) }}" class="chapter-media" {{ scrub('scale-in', 10, 55) }} tabindex="-1" aria-hidden="true">
                <x-project-cover :project="$project" />
            </a>
            <p class="mt-6" {{ scrub('fade-up', 30, 50) }}>
                <a href="{{ lroute('project.show', $project) }}" class="link-arrow"><span>{{ __('site.work.case') }}</span>{{ icon('arrow-right') }}<span class="sr-only">: {{ $project->getTranslation('title') }}</span></a>
            </p>
        </div>
    </div>
</article>
