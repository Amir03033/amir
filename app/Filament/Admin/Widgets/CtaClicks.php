<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * "Werk met mij": which placement brings people to the Work-with-me page (cta_click) and
 * to jebbari.nl (cta_jebbari, with UTM utm_campaign = the same placement).
 */
class CtaClicks extends BaseWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = '"Werk met mij"-kliks per plek (30 dagen)';

    public const PLACEMENTS = [
        'nav' => 'Menu',
        'logo' => 'Onder het logo',
        'home' => 'Homepage',
        'hero' => 'Hero-zin',
        'about' => 'Over mij',
        'case' => 'Einde case',
        'post' => 'Einde notitie',
        'footer' => 'Footer',
        'work-with-me' => 'Werk met mij-pagina',
    ];

    /** @return array<string, array{page: int, jebbari: int, mail: int}> */
    public static function counts(int $days = 30): array
    {
        $rows = Event::query()
            ->whereIn('name', ['cta_click', 'cta_jebbari', 'cta_mail'])
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('name, label, COUNT(*) as n')
            ->groupBy('name', 'label')
            ->get();

        $out = array_fill_keys(array_keys(self::PLACEMENTS), ['page' => 0, 'jebbari' => 0, 'mail' => 0]);
        foreach ($rows as $row) {
            // "work-with-me:service" and "work-with-me:case" count under the page itself.
            $placement = explode(':', (string) $row->label)[0];
            if (! isset($out[$placement])) {
                continue;
            }
            $key = ['cta_click' => 'page', 'cta_jebbari' => 'jebbari', 'cta_mail' => 'mail'][$row->name];
            $out[$placement][$key] += (int) $row->n;
        }

        return $out;
    }

    protected function getStats(): array
    {
        $stats = [];
        foreach (self::counts() as $placement => $c) {
            $stats[] = Stat::make(self::PLACEMENTS[$placement], $c['jebbari'])
                ->description($placement === 'work-with-me'
                    ? "naar jebbari.nl · {$c['mail']} mail"
                    : "naar jebbari.nl · {$c['page']} naar Werk met mij")
                ->descriptionIcon('heroicon-m-arrow-top-right-on-square')
                ->color($c['jebbari'] + $c['page'] ? 'success' : 'gray');
        }

        return $stats;
    }
}
