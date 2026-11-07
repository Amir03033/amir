<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SiteCheck;
use Illuminate\Support\Facades\Cache;

/** Public status page for my live projects, built from `site:check` measurements, like a ServerScope status page. */
class StatusController extends Controller
{
    public function index()
    {
        // Show project names in the visitor's language (checks store one label).
        $titles = Project::all()->filter->hasDemo()->mapWithKeys(fn ($p) => [rtrim($p->demo_url, '/') => $p->getTranslation('title')]);

        $sites = Cache::remember('status-page-'.app()->getLocale(), 300, function () use ($titles) {
            return SiteCheck::query()
                ->where('checked_at', '>=', now()->subDays(35))
                ->orderByDesc('checked_at')
                ->get(['label', 'url'])
                ->unique('url')
                ->sortBy('label')
                ->map(function ($row) {
                    $latest = SiteCheck::where('url', $row->url)->latest('checked_at')->first();
                    $days = SiteCheck::daily($row->url);

                    return [
                        'label' => $titles[rtrim($row->url, '/')] ?? $row->label,
                        'url' => $row->url,
                        'host' => parse_url($row->url, PHP_URL_HOST),
                        'latest' => $latest,
                        'uptime' => SiteCheck::uptime($row->url, 30),
                        'avg_ms' => (int) round($days->pluck('avg_ms')->filter()->avg() ?? 0) ?: null,
                        'days' => $days,
                        'outages' => SiteCheck::outages($row->url)->take(5),
                    ];
                })
                ->values();
        });

        $down = $sites->filter(fn ($s) => ! $s['latest']?->ok)->count();

        return view('status', [
            'sites' => $sites,
            'down' => $down,
            'lastCheck' => $sites->max(fn ($s) => $s['latest']?->checked_at),
        ]);
    }
}
