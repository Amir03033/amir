<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([Project::class, Blog::class] as $model) {
            $model::saved(fn () => Cache::forget('palette-nl') | Cache::forget('palette-en'));
            $model::deleted(fn () => Cache::forget('palette-nl') | Cache::forget('palette-en'));
        }

        // Searchable items for the ⌘K command palette, cached per language.
        View::composer('components.command-palette', function ($view) {
            $view->with('paletteItems', Cache::remember('palette-'.app()->getLocale(), 600, fn () => [
                'projects' => Project::orderByDesc('featured')->orderBy('sort_order')->get()
                    ->map(fn ($p) => ['title' => $p->getTranslation('title'), 'hint' => implode(' · ', array_slice($p->tagList(), 0, 3)), 'url' => route('project.show', $p)])
                    ->values()->all(),
                'notes' => Blog::published()->latest('published_at')->get()
                    ->map(fn ($b) => ['title' => $b->getTranslation('title'), 'hint' => $b->publishedDate()->format('Y-m-d'), 'url' => route('blog.show', $b->slug)])
                    ->values()->all(),
            ]));
        });
    }
}
