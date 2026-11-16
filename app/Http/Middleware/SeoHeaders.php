<?php

namespace App\Http\Middleware;

use App\Support\Seo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs for every request (also the Filament panel, which has its own middleware stack):
 * - www.amirs.life → amirs.life (301), so only the canonical host is indexed;
 * - X-Robots-Tag: noindex on private/utility URLs (/amir, login, 2FA, Livewire, /ping, /status…);
 * - cheap security headers. HSTS is left to the server (see SEO.md).
 */
class SeoHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonicalHost = parse_url((string) config('seo.url'), PHP_URL_HOST);

        if ($canonicalHost && $request->getHost() === 'www.'.$canonicalHost && $request->isMethod('GET')) {
            return redirect()->to(rtrim((string) config('seo.url'), '/').$request->getRequestUri(), 301);
        }

        $response = $next($request);

        if (Seo::isNoindex()) {
            $response->headers->set('X-Robots-Tag', 'noindex, follow');
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), interest-cohort=()');

        return $response;
    }
}
