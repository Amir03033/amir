<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Support\GeoIp;
use Illuminate\Console\Command;

/** One-off: anonymize IP addresses that were stored in full before IP anonymization existed. */
class AnonymizeVisits extends Command
{
    protected $signature = 'visits:anonymize';

    protected $description = 'Replace full IP addresses in stored visits by their anonymized form';

    public function handle(): int
    {
        $changed = 0;

        Visit::whereNotNull('ip_address')->select(['id', 'ip_address'])->chunkById(1000, function ($visits) use (&$changed) {
            foreach ($visits as $visit) {
                $anon = GeoIp::anonymize($visit->ip_address);
                if ($anon !== $visit->ip_address) {
                    Visit::whereKey($visit->id)->update(['ip_address' => $anon]);
                    $changed++;
                }
            }
        });

        $this->info("Anonymized {$changed} visits.");

        return self::SUCCESS;
    }
}
