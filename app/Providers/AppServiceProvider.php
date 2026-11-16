<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // In production every generated URL (canonical, hreflang, sitemap, OG) uses the canonical origin,
        // even if a request arrives on www or a Plesk preview host.
        if (config('seo.force_canonical_host')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('seo.url'));
            if (str_starts_with((string) config('seo.url'), 'https://')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        // Public forms: small, per-IP budgets. Responses are JSON 429s, shown as a friendly toast.
        RateLimiter::for('ask', fn (Request $r) => Limit::perMinutes(10, 3)->by('ask|'.$r->ip()));
        RateLimiter::for('ideas', fn (Request $r) => Limit::perHour(3)->by('ideas|'.$r->ip()));
        RateLimiter::for('votes', fn (Request $r) => Limit::perHour(30)->by('votes|'.$r->ip()));

        // Links managed in Filament override .env. Skipped before the settings table exists (fresh install, tests booting).
        try {
            \App\Filament\Admin\Pages\SiteSettings::applyToConfig();
        } catch (\Throwable) {
        }

        foreach ([Project::class, Blog::class] as $model) {
            $model::saved(fn () => Cache::forget('terminal-projects-nl') | Cache::forget('terminal-projects-en'));
            $model::deleted(fn () => Cache::forget('terminal-projects-nl') | Cache::forget('terminal-projects-en'));
        }

        // Project list for the terminal toy (⌘K on every page), cached per language.
        View::composer('components.terminal', function ($view) {
            $view->with('terminalProjects', Cache::remember('terminal-projects-'.app()->getLocale(), 600, fn () => Project::published()->ordered()->get()
                ->map(fn ($p) => ['title' => $p->getTranslation('title'), 'line' => $p->oneLiner(), 'url' => lroute('project.show', $p)])
                ->values()->all()));
        });
    }
}
