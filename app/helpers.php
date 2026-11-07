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
