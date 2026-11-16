@props(['project', 'eager' => false])
{{-- One project as a card: screenshot (light/dark), title, one-liner, a few tags. The whole card is the link. --}}
<article class="work-card" data-reveal>
    <a href="{{ lroute('project.show', $project) }}" class="work-card-link" aria-labelledby="card-{{ $project->id }}">
        <div class="work-card-media">
            <x-project-cover :project="$project" :eager="$eager" sizes="(min-width: 768px) 560px, 100vw" />
        </div>
        <div class="work-card-body">
            <h3 id="card-{{ $project->id }}" class="work-card-title">{{ $project->getTranslation('title') }}</h3>
            <p class="work-card-text">{{ $project->oneLiner() }}</p>
            <p class="work-card-meta">
                @foreach(array_slice($project->tagList(), 0, 3) as $tag)<span class="tag">{{ $tag }}</span>@endforeach
                <span class="work-card-arrow" aria-hidden="true">{{ icon('arrow-right') }}</span>
            </p>
        </div>
    </a>
</article>
