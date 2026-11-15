<?php

/*
 * Locale-aware routing: Dutch lives at "/", English under "/en" (route names prefixed "en.").
 */

if (! function_exists('lroute')) {
    function lroute(string $name, mixed $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return route(($locale === 'en' ? 'en.' : '').$name, $parameters);
    }
}

if (! function_exists('alternate_url')) {
    /** The current page in another language; falls back to that language's home page. */
    function alternate_url(string $locale): string
    {
        $route = request()->route();
        $name = $route?->getName();

        if (! $name || ! Route::has($name)) {
            return lroute('home', [], $locale);
        }

        $base = str_starts_with($name, 'en.') ? substr($name, 3) : $name;

        return Route::has($base) && Route::has('en.'.$base)
            ? lroute($base, $route->parameters(), $locale)
            : lroute('home', [], $locale);
    }
}

if (! function_exists('icon')) {
    /**
     * Inline a Lucide icon from resources/icons (copied by scripts/icons.mjs). Decorative by default.
     */
    function icon(string $name, string $class = ''): \Illuminate\Support\HtmlString
    {
        static $cache = [];

        if (! isset($cache[$name])) {
            $path = resource_path("icons/{$name}.svg");
            $svg = is_file($path) ? file_get_contents($path) : '';
            $svg = preg_replace('/<!--.*?-->/s', '', $svg);
            // Keep only the shapes; the wrapper is rebuilt below with our size and stroke.
            $cache[$name] = preg_match('/<svg[^>]*>(.*)<\/svg>/s', $svg, $m) ? trim(preg_replace('/\s+/', ' ', $m[1])) : '';
        }

        $classes = trim('icon '.$class);

        return new \Illuminate\Support\HtmlString(
            '<svg class="'.e($classes).'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.$cache[$name].'</svg>'
        );
    }
}

if (! function_exists('scrub')) {
    /**
     * Attributes for a scroll-scrubbed child of a pinned stage: CSS scroll timelines read the
     * custom properties, the JS fallback reads the data attributes. Range is a % of the pin.
     */
    function scrub(string $effect, int $from = 0, int $to = 100): \Illuminate\Support\HtmlString
    {
        return new \Illuminate\Support\HtmlString(sprintf(
            'data-scrub="%s" data-from="%d" data-to="%d" style="--from:%d%%;--to:%d%%"',
            e($effect), $from, $to, $from, $to
        ));
    }
}
