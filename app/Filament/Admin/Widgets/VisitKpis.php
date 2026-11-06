<?php

namespace App\Filament\Admin\Widgets;

use App\Support\VisitStats;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitKpis extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $today = VisitStats::visitors(today());
        $yesterday = VisitStats::visitors(today()->subDay(), today());
        $week = VisitStats::visitors(now()->subDays(7));
        $prevWeek = VisitStats::visitors(now()->subDays(14), now()->subDays(7));
        $views30 = VisitStats::views(now()->subDays(30));
        $visitors30 = VisitStats::visitors(now()->subDays(30));
        $mobile = VisitStats::between(now()->subDays(30))->where('device_type', 'mobile')->count();
        $daily = VisitStats::daily(14);

        return [
            $this->trend(Stat::make('Bezoekers vandaag', $today), VisitStats::change($today, $yesterday), 'gisteren')
                ->chart($daily['visitors']),
            $this->trend(Stat::make('Bezoekers 7 dagen', $week), VisitStats::change($week, $prevWeek), 'vorige week')
                ->chart(array_slice($daily['visitors'], -7)),
            Stat::make("Pagina's per bezoeker", $visitors30 ? number_format($views30 / $visitors30, 1, ',', '') : '—')
                ->description("{$views30} paginaweergaven in 30 dagen")
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->chart($daily['views'])
                ->color('primary'),
            Stat::make('Mobiel', $views30 ? round($mobile / $views30 * 100).'%' : '—')
                ->description('van alle weergaven (30 dagen)')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('gray'),
        ];
    }

    private function trend(Stat $stat, ?int $change, string $versus): Stat
    {
        if ($change === null) {
            return $stat->description("geen data van {$versus}")->color('gray');
        }

        return $stat
            ->description(($change >= 0 ? '+' : '').$change."% t.o.v. {$versus}")
            ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($change >= 0 ? 'success' : 'danger');
    }
}
