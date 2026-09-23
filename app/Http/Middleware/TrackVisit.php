<?php

namespace App\Http\Middleware;

use App\Jobs\ResolveVisitCountry;
use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Jenssegers\Agent\Agent;

class TrackVisit
{
    protected array $blockedUserAgentPatterns = [
        'curl/',
        'Go-http-client',
        'python-requests',
        'Software Security Research',
        'Scrapy',
        'PostmanRuntime',
        'HeadlessChrome',
    ];

    protected array $suspiciousPathPatterns = [
        'wp-config',
        '.env',
        'wp-login',
        'wp-admin',
        '.git',
        'phpinfo',
        'config.php',
        '.php.bak',
    ];

    public function handle(Request $request, Closure $next)
    {
        $userAgent = (string) $request->userAgent();

        // Bekende crawlers (Google, Bing, etc. — pakket-gebaseerd)
        $crawlerDetect = new CrawlerDetect();
        if ($crawlerDetect->isCrawler($userAgent)) {
            return $next($request);
        }

        // Eigen blocklist voor scripts/scanners die niet als "crawler" herkend worden
        foreach ($this->blockedUserAgentPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return $next($request);
            }
        }

        // Aanvalspogingen naar gevoelige bestanden — nooit tracken
        foreach ($this->suspiciousPathPatterns as $pattern) {
            if (stripos($request->path(), $pattern) !== false) {
                return $next($request);
            }
        }

        if (!$request->is('amir*') && !$request->is('livewire*')) {
            $agent = new Agent();
            $agent->setUserAgent($userAgent);

            $visit = Visit::create([
                'ip_hash'     => hash('sha256', $request->ip() . config('app.key')),
                'ip_address'  => $request->ip(),
                'url'         => $request->path(),
                'referrer'    => $request->headers->get('referer'),
                'user_agent'  => $userAgent,
                'browser'     => $agent->browser(),
                'platform'    => $agent->platform(),
                'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
                'utm_source'  => $request->query('utm_source'),
                'utm_campaign'=> $request->query('utm_campaign'),
                'session_id'  => $request->session()->getId(),
            ]);

            ResolveVisitCountry::dispatch($visit->id, $request->ip());
        }

        return $next($request);
    }
}