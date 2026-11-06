<?php

namespace App\Filament\Admin\Widgets;

use App\Models\SiteCheck;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Same numbers as the dashboard on the homepage, measured by `php artisan site:check`. */
class PortfolioHealth extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $d = SiteCheck::dashboard();
        $trend = $d['hours']->map(fn ($h) => $h['state'] === null ? 0 : ($h['state'] ? 1 : 0))->all();

        return [
            Stat::make('Gezondheid', $d['score'] !== null ? $d['score'].'/100' : '—')
                ->description($d['total'] ? "{$d['up']}/{$d['total']} sites online · {$d['incidents']} incidenten" : 'Nog geen metingen')
                ->descriptionIcon($d['incidents'] ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($d['incidents'] ? 'danger' : 'success')
                ->chart($trend),
            Stat::make('Uptime (7 dagen)', $d['uptime_7d'] !== null ? $d['uptime_7d'].'%' : '—')
                ->description('Alle live projecten samen')
                ->color('success'),
            Stat::make('Gem. responstijd', $d['avg_ms'] !== null ? $d['avg_ms'].' ms' : '—')
                ->description($d['slowest'] ? "Traagst: {$d['slowest']['host']} ({$d['slowest']['ms']} ms)" : 'Laatste meting')
                ->color('primary'),
            Stat::make('Laatste meting', $d['last_check']?->diffForHumans() ?? '—')
                ->description('Elke 10 minuten via de scheduler')
                ->color($d['last_check'] && $d['last_check']->gt(now()->subMinutes(30)) ? 'success' : 'warning'),
        ];
    }
}
