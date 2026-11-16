@props(['project', 'index' => 1])
{{-- One featured project: meta, title, one-liner, cover and a link. It fades in once; nothing is pinned or scrubbed. --}}
@php
    $meta = collect([$project->year, $project->getTranslation('role') ?: null])->filter()->implode(' — ');
@endphp
<article class="chapter" aria-labelledby="chapter-{{ $project->id }}">
    <div class="container chapter-inner" data-reveal>
        @if($meta)<p class="text-small text-muted">{{ $meta }}</p>@endif
        <h3 id="chapter-{{ $project->id }}" class="statement mx-auto mt-4 max-w-[16ch]">{{ $project->getTranslation('title') }}</h3>
        <p class="lead mx-auto mt-4 max-w-xl">{{ $project->oneLiner() }}</p>
        <a href="{{ lroute('project.show', $project) }}" class="chapter-media" tabindex="-1" aria-hidden="true">
            <x-project-cover :project="$project" />
        </a>
        <p class="mt-6">
            <a href="{{ lroute('project.show', $project) }}" class="link-arrow"><span>{{ __('site.work.case') }}</span>{{ icon('arrow-right') }}<span class="sr-only">: {{ $project->getTranslation('title') }}</span></a>
        </p>
    </div>
</article>
