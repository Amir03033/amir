<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SiteCheck extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'ok' => 'boolean',
        'checked_at' => 'datetime',
    ];

    /**
     * Everything the dashboard hero needs, built only from stored measurements.
     *
     * @return array{sites: Collection, score: ?int, up: int, total: int, incidents: int, avg_ms: ?int, slowest: ?array, uptime_7d: ?float, hours: Collection, last_check: ?\Illuminate\Support\Carbon}
     */
    public static function dashboard(): array
    {
        $recent = static::query()
            ->where('checked_at', '>=', now()->subDays(7))
            ->orderByDesc('checked_at')
            ->get();

        $sites = $recent->groupBy('label')->map(fn (Collection $checks) => [
            'label' => $checks->first()->label,
            'url' => $checks->first()->url,
            'host' => parse_url($checks->first()->url, PHP_URL_HOST),
            'latest' => $checks->first(),
        ])->sortBy('label')->values();

        $total = $sites->count();
        $up = $sites->filter(fn ($s) => $s['latest']->ok)->count();
        $latestMs = $sites->pluck('latest.response_ms')->filter(fn ($v) => $v !== null);
        $slowest = $sites->filter(fn ($s) => $s['latest']->response_ms !== null)->sortByDesc('latest.response_ms')->first();

        // One cell per hour for the last 24h: null = no data, true = all checks ok, false = at least one failure.
        $day = $recent->where('checked_at', '>=', now()->subDay());
        $hours = collect(range(23, 0))->map(function (int $ago) use ($day) {
            $from = now()->subHours($ago + 1);
            $to = now()->subHours($ago);
            $checks = $day->filter(fn ($c) => $c->checked_at > $from && $c->checked_at <= $to);

            return [
                'at' => $to,
                'state' => $checks->isEmpty() ? null : $checks->every(fn ($c) => $c->ok),
                'count' => $checks->count(),
            ];
        });

        return [
            'sites' => $sites,
            'score' => $total ? (int) round($up / $total * 100) : null,
            'up' => $up,
            'total' => $total,
            'incidents' => $total - $up,
            'avg_ms' => $latestMs->isNotEmpty() ? (int) round($latestMs->avg()) : null,
            'slowest' => $slowest ? ['host' => $slowest['host'], 'ms' => $slowest['latest']->response_ms] : null,
            'uptime_7d' => $recent->isNotEmpty() ? round($recent->where('ok', true)->count() / $recent->count() * 100, 2) : null,
            'hours' => $hours,
            'last_check' => $recent->first()?->checked_at,
        ];
    }

    /** Latest measurement per URL, for live dots on project cards. */
    public static function latestByUrl(): Collection
    {
        return static::query()
            ->where('checked_at', '>=', now()->subDay())
            ->orderByDesc('checked_at')
            ->get()
            ->unique('url')
            ->keyBy(fn ($c) => rtrim($c->url, '/'));
    }
}
