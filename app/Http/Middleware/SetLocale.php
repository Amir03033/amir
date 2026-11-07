<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/** The URL decides the language: "/en/..." is English, everything else Dutch (so search engines can index both). */
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->segment(1) === 'en' ? 'en' : 'nl';

        // Posts from the site (contact form, events) say which language the visitor was reading.
        if ($request->isMethod('POST') && in_array($request->input('locale'), ['nl', 'en'], true)) {
            $locale = $request->input('locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
