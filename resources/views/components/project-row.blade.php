@props(['project'])
{{-- Ruled project row. Desktop hover shows the screenshot following the cursor (preview.js). --}}
<li {{ $attributes->merge(['data-reveal' => true]) }}>
    <a href="{{ lroute('project.show', $project) }}" class="project-row" @if($project->coverUrl()) data-preview="{{ $project->coverUrl() }}" data-preview-dark="{{ $project->coverDarkUrl() }}" @endif>
        <span class="label tabular">{{ $project->year ?: '—' }}</span>
        <span class="min-w-0">
            <span class="block text-h3 font-semibold">{{ $project->getTranslation('title') }}</span>
            <span class="mt-1 block text-small text-muted">{{ $project->oneLiner() }}</span>
        </span>
        <span class="project-row-tags">
            @foreach(array_slice($project->tagList(), 0, 3) as $tag)
                <span class="tag">{{ $tag }}</span>
            @endforeach
        </span>
        {{ icon('arrow-right', 'project-row-arrow') }}
    </a>
</li>
