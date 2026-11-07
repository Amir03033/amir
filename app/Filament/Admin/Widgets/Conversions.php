<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Event;
use App\Models\Visit;
use App\Support\VisitStats;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** What visitors actually do: download the CV, book a call, send a message, open a case. */
class Conversions extends BaseWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Conversies (30 dagen)';

    protected function getStats(): array
    {
        $since = now()->subDays(30);
        $count = fn (string $name) => Event::where('name', $name)->where('created_at', '>=', $since)->count();
        $visitors = VisitStats::visitors($since);
        $contacts = $count('contact_submit');
        $caseViews = Visit::where('created_at', '>=', $since)
            ->where(fn ($q) => $q->where('url', 'like', 'project/%')->orWhere('url', 'like', 'en/project/%'))
            ->count();
        $topOutbound = Event::where('name', 'outbound')->where('created_at', '>=', $since)
            ->selectRaw('label, COUNT(*) as n')->groupBy('label')->orderByDesc('n')->first();

        return [
            Stat::make('Contactberichten', $contacts)
                ->description($visitors ? round($contacts / $visitors * 100, 1).'% van '.$visitors.' bezoekers' : 'nog geen bezoekers')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($contacts ? 'success' : 'gray'),
            Stat::make('CV-downloads', $count('cv_download'))
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->description('PDF via /cv.pdf')
                ->color('primary'),
            Stat::make('"Plan een gesprek"-kliks', $count('booking_click'))
                ->descriptionIcon('heroicon-m-calendar')
                ->description(config('site.booking_url') ? 'naar je afsprakenlink' : 'nog geen afsprakenlink ingesteld')
                ->color('primary'),
            Stat::make('Cases bekeken', $caseViews)
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->description($topOutbound ? 'meest geklikt: '.$topOutbound->label.' ('.$topOutbound->n.')' : 'projectpagina-weergaven')
                ->color('gray'),
        ];
    }
}
