<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Bezoekers vandaag', Visit::whereDate('created_at', today())->count()),
            Stat::make('Bezoekers deze week', Visit::where('created_at', '>=', now()->subWeek())->count()),
            Stat::make('Top pagina', Visit::select('url')->groupBy('url')
                ->orderByRaw('COUNT(*) DESC')->first()?->url ?? '-'),
        ];
    }
}