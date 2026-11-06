<?php

namespace App\Filament\Admin\Resources\VisitResource\Pages;

use App\Filament\Admin\Resources\VisitResource;
use App\Filament\Admin\Resources\VisitResource\Widgets\VisitBreakdown;
use App\Filament\Admin\Resources\VisitResource\Widgets\VisitTrend;
use App\Filament\Admin\Widgets\VisitKpis;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListVisits extends ListRecords
{
    protected static string $resource = VisitResource::class;

    protected ?string $subheading = 'Paginaweergaven op je portfolio. Bots, scanners en je eigen beheer worden niet meegeteld.';

    protected function getHeaderWidgets(): array
    {
        return [VisitKpis::class, VisitTrend::class, VisitBreakdown::class];
    }

    public function getTabs(): array
    {
        return [
            'today' => Tab::make('Vandaag')->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today())),
            'week' => Tab::make('7 dagen')->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7))),
            'month' => Tab::make('30 dagen')->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(30))),
            'all' => Tab::make('Alles'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'week';
    }
}
