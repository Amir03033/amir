<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Everything search engines and AI assistants read: titles, descriptions, canonical/hreflang
 * and one connected JSON-LD graph per page. All facts come from config/seo.php.
 * See SEO.md for the structured data map.
 */
class Seo
{
    public const TITLE_MAX = 60;

    public const DESCRIPTION_MAX = 155;

    /** Title and description for a named page from lang/{locale}/seo.php. */
    public static function page(string $key): array
    {
        return [
            'title' => __("seo.pages.{$key}.title"),
            'description' => __("seo.pages.{$key}.description"),
        ];
    }

    public static function title(string $title): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', $title)), self::TITLE_MAX - 1, '…');
    }

    public static function description(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));

        return mb_strlen($text) <= self::DESCRIPTION_MAX ? $text : rtrim(Str::limit($text, self::DESCRIPTION_MAX - 1, ''), ' ,.;:').'…';
    }

    public static function projectTitle(Project $project): string
    {
        if ($own = trim((string) $project->getTranslation('seo_title'))) {
            return self::title($own);
        }
        $name = $project->getTranslation('title');
        $long = __('seo.pages.project.title', ['title' => $name]);

        return mb_strlen($long) <= self::TITLE_MAX ? $long : self::title(__('seo.pages.project.title_short', ['title' => $name]));
    }

    public static function projectDescription(Project $project): string
    {
        if ($own = trim((string) $project->getTranslation('seo_description'))) {
            return self::description($own);
        }
        $line = trim((string) $project->getTranslation('summary')) ?: $project->excerpt(300);

        return self::description(__('seo.pages.project.description', ['title' => $project->getTranslation('title'), 'line' => $line]));
    }

    /** Meta description of a note: the editor's own, else the excerpt, padded with who wrote it when short. */
    public static function noteDescription(Blog $blog): string
    {
        if ($own = trim((string) $blog->getTranslation('meta_description'))) {
            return self::description($own);
        }
        $excerpt = $blog->excerpt(200);
        $by = __('seo.pages.note.description', ['title' => $blog->getTranslation('title')]);

        return self::description(mb_strlen($excerpt) < 80 ? trim($by.' '.$excerpt) : $excerpt);
    }

    public static function noteTitle(Blog $blog): string
    {
        return self::title(trim((string) $blog->getTranslation('meta_title')) ?: __('seo.pages.note.title', ['title' => $blog->getTranslation('title')]));
    }

    /** The canonical URL of the current request (generated on the canonical host in production). */
    public static function canonical(): string
    {
        $path = trim(request()->path(), '/');

        return rtrim(url($path === '' ? '/' : $path), '/') ?: url('/');
    }

    /**
     * nl/en URLs of the current page, or null when the page has no counterpart in the other
     * language (404, login…), so no wrong hreflang is ever printed.
     *
     * @return array{nl: string, en: string}|null
     */
    public static function alternates(): ?array
    {
        $name = request()->route()?->getName();
        if (! $name || in_array($name, ['styleguide', 'login', 'two-factor.check'], true)) {
            return null;
        }

        $base = str_starts_with($name, 'en.') ? substr($name, 3) : $name;
        if (! Route::has($base) || ! Route::has('en.'.$base)) {
            return null;
        }

        return ['nl' => alternate_url('nl'), 'en' => alternate_url('en')];
    }

    public static function isNoindex(): bool
    {
        return request()->is(...config('seo.noindex', []));
    }

    public static function home(?string $locale = null): string
    {
        return lroute('home', [], $locale ?? app()->getLocale());
    }

    // ---------------------------------------------------------------------------------------
    // JSON-LD
    // ---------------------------------------------------------------------------------------

    public static function person(): array
    {
        $locale = app()->getLocale();
        // Uploaded profile photo, else the uploaded about photo, else the bundled 960×1200 portrait.
        $uploaded = \App\Models\Setting::media('avatar') ?? \App\Models\Setting::media('about');
        $image = $uploaded
            ? ['@type' => 'ImageObject', 'url' => $uploaded]
            : ['@type' => 'ImageObject', 'url' => asset('images/about/amir-960.webp'), 'width' => 960, 'height' => 1200];

        return [
            '@type' => 'Person',
            '@id' => config('seo.person_id'),
            'name' => config('seo.name'),
            'givenName' => config('seo.given_name'),
            'familyName' => config('seo.family_name'),
            'url' => self::home('nl'),
            'mainEntityOfPage' => self::home($locale),
            'email' => 'mailto:'.config('seo.email'),
            'jobTitle' => config("seo.job_title.{$locale}"),
            'description' => config("seo.description.{$locale}"),
            'image' => $image,
            'worksFor' => ['@id' => config('seo.company.id')],
            'address' => array_map(fn ($city) => [
                '@type' => 'PostalAddress',
                'addressLocality' => $city,
                'addressCountry' => config('seo.country'),
            ], config('seo.locations')),
            'knowsAbout' => config('seo.knows_about'),
            'sameAs' => array_values(array_unique([...config('seo.same_as'), config('seo.company.person_id')])),
        ];
    }

    /** Jebbari, by the same @id jebbari.nl uses, so both graphs merge into one organisation. */
    public static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => config('seo.company.id'),
            'name' => config('seo.company.name'),
            'url' => config('seo.company.url'),
            'email' => config('seo.email'),
            'founder' => ['@id' => config('seo.person_id')],
            'areaServed' => array_map(fn ($city) => ['@type' => 'City', 'name' => $city], config('seo.locations')),
        ];
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => config('seo.website_id'),
            'url' => self::home('nl'),
            'name' => config('seo.site_name'),
            'alternateName' => config('seo.name'),
            'description' => config('seo.description.'.app()->getLocale()),
            'inLanguage' => ['nl', 'en'],
            'publisher' => ['@id' => config('seo.person_id')],
            'about' => ['@id' => config('seo.person_id')],
        ];
    }

    /** @param list<array{0: string, 1: string}> $crumbs [name, url] after Home */
    public static function breadcrumbs(string $canonical, array $crumbs): array
    {
        $items = [[__('seo.breadcrumbs.home'), self::home()], ...$crumbs];

        return [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical.'#breadcrumb',
            'itemListElement' => array_map(fn ($c, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $c[0],
                'item' => $c[1],
            ], $items, array_keys($items)),
        ];
    }

    public static function project(Project $project, string $canonical): array
    {
        $jebbari = $project->jebbariUrl();

        return array_filter([
            '@type' => 'CreativeWork',
            '@id' => $canonical.'#project',
            'name' => $project->getTranslation('title'),
            'headline' => $project->getTranslation('title'),
            'description' => self::projectDescription($project),
            'url' => $canonical,
            'image' => $project->coverUrl() ?: self::ogImage('project', $project->id),
            'creativeWorkStatus' => match ($project->status) { 'live' => 'Published', 'building' => 'Incomplete', default => null },
            'dateCreated' => $project->year ? (string) $project->year : null,
            'dateModified' => $project->updated_at?->toAtomString(),
            'inLanguage' => app()->getLocale(),
            'keywords' => implode(', ', $project->tagList()) ?: null,
            'author' => ['@id' => config('seo.person_id')],
            'creator' => ['@id' => config('seo.person_id')],
            'producer' => $jebbari ? ['@id' => config('seo.company.id')] : null,
            'mainEntityOfPage' => ['@id' => $canonical.'#webpage'],
            'sameAs' => array_values(array_filter([$project->hasDemo() ? $project->demo_url : null, $jebbari ?: null])) ?: null,
        ]);
    }

    public static function blogPosting(Blog $blog, string $canonical): array
    {
        return array_filter([
            '@type' => 'BlogPosting',
            '@id' => $canonical.'#article',
            'headline' => Str::limit($blog->getTranslation('title'), 110, '…'),
            'description' => self::noteDescription($blog),
            'datePublished' => $blog->publishedDate()->toAtomString(),
            'dateModified' => ($blog->updated_at ?? $blog->publishedDate())->toAtomString(),
            'url' => $canonical,
            'inLanguage' => app()->getLocale(),
            'image' => $blog->image ? asset('storage/'.$blog->image) : self::ogImage('blog', $blog->id),
            'keywords' => $blog->tagList() ? implode(', ', $blog->tagList()) : null,
            'author' => ['@id' => config('seo.person_id')],
            'publisher' => ['@id' => config('seo.person_id')],
            'mainEntityOfPage' => ['@id' => $canonical.'#webpage'],
        ]);
    }

    /** ItemList of URLs, for collection pages (projects, notes). */
    public static function itemList(string $canonical, iterable $urls): array
    {
        $items = [];
        foreach ($urls as $i => [$name, $url]) {
            $items[] = ['@type' => 'ListItem', 'position' => count($items) + 1, 'name' => $name, 'url' => $url];
        }

        return ['@type' => 'ItemList', '@id' => $canonical.'#list', 'itemListElement' => $items];
    }

    public static function ogImage(string $type, int|string $id): string
    {
        return $type === 'page'
            ? route('og.page', ['key' => $id, 'lang' => app()->getLocale()])
            : route('og', ['type' => $type, 'id' => $id, 'lang' => app()->getLocale()]);
    }

    /**
     * The full graph for a page: Person, Organization (Jebbari), WebSite, the WebPage itself,
     * breadcrumbs on inner pages, plus page-specific nodes.
     *
     * @param  array{type?: string, title: string, description: string, canonical: string, image?: ?string, crumbs?: array, nodes?: array, mainEntity?: ?string}  $page
     */
    public static function graph(array $page): array
    {
        $canonical = $page['canonical'];
        $type = $page['type'] ?? 'WebPage';
        $crumbs = $page['crumbs'] ?? [];

        $webpage = array_filter([
            '@type' => $type,
            '@id' => $canonical.'#webpage',
            'url' => $canonical,
            'name' => $page['title'],
            'description' => $page['description'],
            'inLanguage' => app()->getLocale(),
            'isPartOf' => ['@id' => config('seo.website_id')],
            'about' => ['@id' => config('seo.person_id')],
            'mainEntity' => ['@id' => $page['mainEntity'] ?? ($type === 'ProfilePage' ? config('seo.person_id') : null)],
            'primaryImageOfPage' => ! empty($page['image']) ? ['@type' => 'ImageObject', 'url' => $page['image']] : null,
            'breadcrumb' => $crumbs ? ['@id' => $canonical.'#breadcrumb'] : null,
        ], fn ($v) => $v !== null && $v !== ['@id' => null]);

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter([
                self::person(),
                self::organization(),
                self::website(),
                $webpage,
                $crumbs ? self::breadcrumbs($canonical, $crumbs) : null,
                ...($page['nodes'] ?? []),
            ])),
        ];
    }

    public static function json(array $graph): string
    {
        return json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }
}
