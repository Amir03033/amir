<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class TrackVisit
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('amir*') && !$request->is('livewire*')) {
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());

            Visit::create([
                'ip_hash'     => hash('sha256', $request->ip() . config('app.key')),
                'url'         => $request->path(),
                'referrer'    => $request->headers->get('referer'),
                'user_agent'  => $request->userAgent(),
                'browser'     => $agent->browser(),
                'platform'    => $agent->platform(),
                'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
                'utm_source'  => $request->query('utm_source'),
                'utm_campaign'=> $request->query('utm_campaign'),
                'session_id'  => $request->session()->getId(),
            ]);
        }

        return $next($request);
    }
}