<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/** Stores daily totals before raw visits are pruned, so long-term charts keep working. */
class RollupVisits extends Command
{
    protected $signature = 'visits:rollup';

    protected $description = 'Summarize raw visits into one row per day (visit_days)';

    public function handle(): int
    {
        $rows = Visit::query()
            ->where('created_at', '<', today())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views, COUNT(DISTINCT ip_hash) as visitors')
            ->groupBy('date')
            ->get()
            ->map(fn ($r) => ['date' => $r->date, 'views' => (int) $r->views, 'visitors' => (int) $r->visitors])
            ->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('visit_days')->upsert($chunk, ['date'], ['views', 'visitors']);
        }

        $this->info('Rolled up '.count($rows).' days.');

        return self::SUCCESS;
    }
}
