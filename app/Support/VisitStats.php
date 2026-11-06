<?php

namespace App\Support;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/** Aggregates for the visits pages in Filament. "Visitors" are unique ip_hash values. */
class VisitStats
{
    public static function between($from, $to = null): Builder
    {
        return Visit::query()->where('created_at', '>=', $from)->when($to, fn ($q) => $q->where('created_at', '<', $to));
    }

    public static function views($from, $to = null): int
    {
        return static::between($from, $to)->count();
    }

    public static function visitors($from, $to = null): int
    {
        return static::between($from, $to)->distinct()->count('ip_hash');
    }

    /** Percentage change, or null when there is nothing to compare with. */
    public static function change(int|float $now, int|float $before): ?int
    {
        return $before > 0 ? (int) round(($now - $before) / $before * 100) : null;
    }

    /** @return array{labels: list<string>, views: list<int>, visitors: list<int>} */
    public static function daily(int $days): array
    {
        return Cache::remember("visits-daily-{$days}", 300, function () use ($days) {
            $rows = static::between(now()->subDays($days - 1)->startOfDay())
                ->selectRaw('DATE(created_at) as d, COUNT(*) as views, COUNT(DISTINCT ip_hash) as visitors')
                ->groupBy('d')
                ->get()
                ->keyBy('d');

            // Days whose raw visits were pruned come from the daily totals.
            $rolled = \Illuminate\Support\Facades\DB::table('visit_days')
                ->where('date', '>=', now()->subDays($days - 1)->toDateString())
                ->get()
                ->keyBy(fn ($r) => (string) $r->date);
            foreach ($rolled as $d => $r) {
                $rows[$d] ??= (object) ['views' => $r->views, 'visitors' => $r->visitors];
            }

            $dates = collect(range($days - 1, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));

            return [
                'labels' => $dates->map(fn ($d) => date('d-m', strtotime($d)))->all(),
                'views' => $dates->map(fn ($d) => (int) ($rows[$d]->views ?? 0))->all(),
                'visitors' => $dates->map(fn ($d) => (int) ($rows[$d]->visitors ?? 0))->all(),
            ];
        });
    }

    /** Top values of a column (or a computed key) with share of total, for bar lists. */
    public static function top(string $column, int $days = 30, int $limit = 6, ?callable $key = null): Collection
    {
        return Cache::remember("visits-top-{$column}-{$days}", 300, function () use ($column, $days, $limit, $key) {
            $rows = static::between(now()->subDays($days))->get([$column]);
            $total = max($rows->count(), 1);

            return $rows
                ->groupBy(fn ($v) => $key ? $key($v->{$column}) : ($v->{$column} ?: '—'))
                ->map->count()
                ->sortDesc()
                ->take($limit)
                ->map(fn ($count, $label) => ['label' => (string) $label, 'count' => $count, 'pct' => round($count / $total * 100)])
                ->values();
        });
    }

    public static function source(?string $referrer): string
    {
        if (! $referrer) {
            return 'Direct';
        }

        $host = preg_replace('/^www\./', '', (string) parse_url($referrer, PHP_URL_HOST));

        return $host === parse_url(config('app.url'), PHP_URL_HOST) ? 'Intern' : ($host ?: 'Direct');
    }

    public static function flag(?string $code): string
    {
        if (! $code || strlen($code) !== 2) {
            return '🌐';
        }

        return mb_chr(0x1F1E6 + ord(strtoupper($code[0])) - 65).mb_chr(0x1F1E6 + ord(strtoupper($code[1])) - 65);
    }
}
