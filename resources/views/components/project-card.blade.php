@props(['project'])

<a href="{{ route('project.show', $project) }}" class="block border border-slate-800 bg-cyber-card rounded-xl overflow-hidden group hover:border-cyan-500/40 transition-all duration-300">
    <div class="h-44 bg-slate-950 overflow-hidden">
        <img src="{{ $project->coverUrl() }}" alt="{{ $project->getTranslation('title') }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500">
    </div>
    <div class="p-5">
        <h3 class="text-base font-bold text-white mb-2">{{ $project->getTranslation('title') }}</h3>
        <p class="text-slate-400 text-sm mb-4 leading-relaxed">{{ $project->excerpt() }}</p>
        <div class="flex flex-wrap gap-1.5 mb-4">
            @foreach($project->tagList() as $tag)
                <span class="px-2 py-0.5 rounded-md border border-slate-800 text-[11px] font-mono text-cyan-400/90">{{ $tag }}</span>
            @endforeach
        </div>
        <div class="flex items-center justify-between text-xs font-mono">
            <span class="text-cyan-400 group-hover:underline">{{ __('messages.portfolio.read_case') }}</span>
            <span class="flex gap-3 text-slate-500">
                @if($project->hasDemo())
                    <span>{{ __('messages.portfolio.live') }}</span>
                @endif
            </span>
        </div>
    </div>
</a>
