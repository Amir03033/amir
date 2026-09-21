<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResolveVisitCountry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 10;

    public function __construct(protected int $visitId, protected string $ip)
    {
    }

    public function handle(): void
    {
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$this->ip}?fields=countryCode");
            $country = $response ? json_decode($response)?->countryCode : null;

            if ($country) {
                Visit::where('id', $this->visitId)->update(['country' => $country]);
            }
        } catch (\Throwable $e) {
            // stil falen, geolocatie is niet kritiek
        }
    }
}