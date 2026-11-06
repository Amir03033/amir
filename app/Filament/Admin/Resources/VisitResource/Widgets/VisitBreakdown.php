<?php

namespace App\Filament\Admin\Resources\VisitResource\Widgets;

use App\Support\VisitStats;
use Filament\Widgets\Widget;

class VisitBreakdown extends Widget
{
    protected static string $view = 'filament.admin.widgets.visit-breakdown';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $devices = ['desktop' => 'Desktop', 'mobile' => 'Mobiel', 'tablet' => 'Tablet'];

        return [
            'cards' => [
                ['title' => "Top pagina's", 'icon' => 'heroicon-o-document-text', 'rows' => VisitStats::top('url', key: fn ($u) => '/'.ltrim((string) $u, '/'))],
                ['title' => 'Bronnen', 'icon' => 'heroicon-o-arrow-top-right-on-square', 'rows' => VisitStats::top('referrer', key: [VisitStats::class, 'source'])],
                ['title' => 'Apparaten', 'icon' => 'heroicon-o-device-phone-mobile', 'rows' => VisitStats::top('device_type', key: fn ($d) => $devices[$d] ?? 'Onbekend')],
                ['title' => 'Landen', 'icon' => 'heroicon-o-globe-europe-africa', 'rows' => VisitStats::top('country', key: fn ($c) => VisitStats::flag($c).' '.($c ?: 'Onbekend'))],
                ['title' => 'Browsers', 'icon' => 'heroicon-o-window', 'rows' => VisitStats::top('browser', key: fn ($b) => $b ?: 'Onbekend')],
                ['title' => 'Besturingssystemen', 'icon' => 'heroicon-o-cpu-chip', 'rows' => VisitStats::top('platform', key: fn ($p) => $p ?: 'Onbekend')],
            ],
        ];
    }
}
