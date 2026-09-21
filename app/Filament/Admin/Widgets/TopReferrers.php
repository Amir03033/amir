<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Visit;
use Filament\Widgets\Widget;

class TopReferrers extends Widget
{
    protected static string $view = 'filament.admin.widgets.top-referrers';

    protected function getViewData(): array
    {
        $top = Visit::whereNotNull('referrer')
            ->get()
            ->groupBy(fn ($visit) => $visit->referrer_domain)
            ->map->count()
            ->sortDesc()
            ->take(5);

        return ['referrers' => $top];
    }
}