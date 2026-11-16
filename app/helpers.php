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

        // Route::view() passes its view name/status as parameters; they are not part of the URL.
        $params = \Illuminate\Support\Arr::except($route->parameters(), ['view', 'data', 'status', 'headers']);

        return Route::has($base) && Route::has('en.'.$base)
            ? lroute($base, $params, $locale)
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

if (! function_exists('seo_sentence')) {
    /**
     * The canonical "who is Amir" sentence from config/seo.php, optionally with the company name
     * linked to jebbari.nl (anchor text always "Jebbari").
     */
    function seo_sentence(bool $link = false, ?string $locale = null): \Illuminate\Support\HtmlString
    {
        $locale ??= app()->getLocale();
        $sentence = e(config("seo.description.{$locale}") ?? config('seo.description.nl'));

        if ($link) {
            $company = e(config('seo.company.name'));
            $anchor = '<a href="'.e(config('seo.company.url')).'" class="link" rel="noopener">'.$company.'</a>';
            // Link the company, not the family name: "… van Jebbari," / "… of Jebbari,".
            $sentence = preg_replace('/\b(van|of) '.preg_quote($company, '/').'\b/u', '$1 '.$anchor, $sentence, 1);
        }

        return new \Illuminate\Support\HtmlString($sentence);
    }
}

if (! function_exists('image_size')) {
    /**
     * [width, height] of an image served from this site (public/ or the storage disk), or null.
     * Cached per file version, so explicit dimensions cost nothing after the first request.
     *
     * @return array{0: int, 1: int}|null
     */
    function image_size(?string $url): ?array
    {
        if (! $url) {
            return null;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        $file = str_starts_with($path, '/storage/')
            ? storage_path('app/public/'.substr($path, strlen('/storage/')))
            : public_path(ltrim($path, '/'));

        if (! is_file($file)) {
            return null;
        }

        return \Illuminate\Support\Facades\Cache::rememberForever('img-size:'.md5($file.filemtime($file)), function () use ($file) {
            $size = @getimagesize($file);

            return $size ? [(int) $size[0], (int) $size[1]] : null;
        });
    }
}

if (! function_exists('can_preview')) {
    /**
     * True for the site owner when logged in to /amir with 2FA passed: only then are drafts
     * (projects and notes) visible on the public URLs, marked as a preview and noindex.
     */
    function can_preview(): bool
    {
        $user = auth()->user();
        if (! $user || ! method_exists($user, 'canAccessPanel')) {
            return false;
        }

        try {
            $panel = \Filament\Facades\Filament::getPanel('Admin');
        } catch (\Throwable) {
            return false;
        }

        return $user->canAccessPanel($panel)
            && session(\App\Http\Middleware\RequireTwoFactor::SESSION_KEY) === $user->getKey();
    }
}

if (! function_exists('review_marks')) {
    /** Highlight "[VERIFY: …]" markers in already-escaped HTML, so they stand out in a preview. */
    function review_marks(string|\Illuminate\Support\HtmlString $html): \Illuminate\Support\HtmlString
    {
        return new \Illuminate\Support\HtmlString(
            preg_replace('/\[VERIFY[^\]]*\]/u', '<mark class="verify">$0</mark>', (string) $html)
        );
    }
}
