<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\SiteCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckSites extends Command
{
    protected $signature = 'site:check';

    protected $description = 'Probe the live project URLs and store the results for the status panel';

    public function handle(): int
    {
        foreach ($this->targets() as $label => $url) {
            $start = hrtime(true);

            try {
                $response = Http::timeout(10)->withHeaders(['User-Agent' => 'amir-jebbari-status/1.0'])->get($url);
                $ms = (int) round((hrtime(true) - $start) / 1e6);
                $ok = $response->successful();
                $code = $response->status();
            } catch (Throwable) {
                $ms = null;
                $ok = false;
                $code = null;
            }

            SiteCheck::create([
                'label' => $label,
                'url' => $url,
                'ok' => $ok,
                'status_code' => $code,
                'response_ms' => $ms,
                'checked_at' => now(),
            ]);

            $this->line(sprintf('%-14s %s %s', $label, $ok ? 'up' : 'down', $ms !== null ? "{$ms}ms" : ''));
        }

        SiteCheck::where('checked_at', '<', now()->subDays(7))->delete();
        Cache::forget('status-panel');
        Cache::forget('status-live');

        return self::SUCCESS;
    }

    /** @return array<string, string> */
    private function targets(): array
    {
        $targets = config('site.status_targets', []);

        if ($serverscope = config('site.serverscope.url')) {
            $targets['ServerScope'] = $serverscope;
        }

        foreach (Project::orderBy('sort_order')->get() as $project) {
            if ($project->hasDemo()) {
                $targets[$project->title['en'] ?? $project->getTranslation('title')] ??= $project->demo_url;
            }
        }

        return $targets;
    }
}
