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

    /**
     * Per-day uptime for one URL over the last $days days (oldest first). Days without checks have uptime null.
     *
     * @return Collection<int, array{date: \Illuminate\Support\Carbon, uptime: ?float, checks: int, avg_ms: ?int}>
     */
    public static function daily(string $url, int $days = 30): Collection
    {
        $rows = static::query()
            ->whereIn('url', [rtrim($url, '/'), rtrim($url, '/').'/'])
            ->where('checked_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->selectRaw('DATE(checked_at) as d, COUNT(*) as checks, SUM(CASE WHEN ok THEN 1 ELSE 0 END) as up, AVG(response_ms) as avg_ms')
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        return collect(range($days - 1, 0))->map(function (int $ago) use ($rows) {
            $date = now()->subDays($ago)->startOfDay();
            $row = $rows[$date->toDateString()] ?? null;

            return [
                'date' => $date,
                'uptime' => $row ? round($row->up / max($row->checks, 1) * 100, 2) : null,
                'checks' => (int) ($row->checks ?? 0),
                'avg_ms' => $row && $row->avg_ms !== null ? (int) round($row->avg_ms) : null,
            ];
        });
    }

    /** Uptime percentage over the given days, or null without data. */
    public static function uptime(string $url, int $days = 30): ?float
    {
        $q = static::query()->whereIn('url', [rtrim($url, '/'), rtrim($url, '/').'/'])->where('checked_at', '>=', now()->subDays($days));
        $total = (clone $q)->count();

        return $total ? round((clone $q)->where('ok', true)->count() / $total * 100, 2) : null;
    }

    /** First stored measurement for a URL ("measured since"). */
    public static function firstCheck(string $url): ?\Illuminate\Support\Carbon
    {
        $first = static::query()->whereIn('url', [rtrim($url, '/'), rtrim($url, '/').'/'])->min('checked_at');

        return $first ? \Illuminate\Support\Carbon::parse($first) : null;
    }

    /**
     * Outages: runs of consecutive failed checks, newest first.
     *
     * @return Collection<int, array{start: \Illuminate\Support\Carbon, end: ?\Illuminate\Support\Carbon, minutes: int, code: ?int}>
     */
    public static function outages(string $url, int $days = 30): Collection
    {
        $checks = static::query()
            ->whereIn('url', [rtrim($url, '/'), rtrim($url, '/').'/'])
            ->where('checked_at', '>=', now()->subDays($days))
            ->orderBy('checked_at')
            ->get(['ok', 'status_code', 'checked_at']);

        $outages = collect();
        $current = null;

        foreach ($checks as $check) {
            if (! $check->ok && ! $current) {
                $current = ['start' => $check->checked_at, 'end' => null, 'code' => $check->status_code];
            } elseif ($check->ok && $current) {
                $current['end'] = $check->checked_at;
                $outages->push($current);
                $current = null;
            }
        }

        if ($current) {
            $outages->push($current);
        }

        return $outages->map(fn ($o) => $o + ['minutes' => (int) $o['start']->diffInMinutes($o['end'] ?? now())])->reverse()->values();
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
