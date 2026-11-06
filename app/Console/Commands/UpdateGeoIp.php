<?php

namespace App\Console\Commands;

use App\Support\GeoIp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateGeoIp extends Command
{
    protected $signature = 'geoip:update {--file= : Use a local dbip-country-lite CSV(.gz) instead of downloading}';

    protected $description = 'Download DB-IP Lite (country) and load it into geoip_ranges for local country lookups';

    public function handle(): int
    {
        $path = $this->option('file') ?: $this->download();

        if (! $path) {
            return self::FAILURE;
        }

        $handle = str_ends_with($path, '.gz') ? gzopen($path, 'r') : fopen($path, 'r');
        $rows = [];
        $count = 0;

        DB::transaction(function () use ($handle, &$rows, &$count) {
            DB::table('geoip_ranges')->delete();

            while (($line = fgetcsv($handle)) !== false) {
                [$from, $to, $country] = array_pad($line, 3, null);
                $fromKey = GeoIp::key((string) $from);
                $toKey = GeoIp::key((string) $to);

                if (! $fromKey || ! $toKey || strlen((string) $country) !== 2) {
                    continue;
                }

                $rows[] = ['ip_from' => $fromKey, 'ip_to' => $toKey, 'country' => $country];

                if (count($rows) === 1000) {
                    DB::table('geoip_ranges')->insert($rows);
                    $count += 1000;
                    $rows = [];
                }
            }

            if ($rows) {
                DB::table('geoip_ranges')->insert($rows);
                $count += count($rows);
            }
        });

        str_ends_with($path, '.gz') ? gzclose($handle) : fclose($handle);

        if (! $this->option('file')) {
            @unlink($path);
        }
        $this->info("Loaded {$count} IP ranges.");

        return self::SUCCESS;
    }

    private function download(): ?string
    {
        // DB-IP publishes one file per month; early in the month the new one may not exist yet.
        foreach ([now(), now()->subMonthNoOverflow()] as $month) {
            $url = 'https://download.db-ip.com/free/dbip-country-lite-'.$month->format('Y-m').'.csv.gz';
            $target = storage_path('app/dbip-country-lite.csv.gz');
            $response = Http::timeout(120)->sink($target)->get($url);

            if ($response->successful()) {
                $this->line("Downloaded {$url}");

                return $target;
            }
        }

        $this->error('Could not download DB-IP Lite.');

        return null;
    }
}
