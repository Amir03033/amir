<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Project extends Model
{
    use \App\Models\Concerns\HasReviewMarkers {
        reviewMarkers as traitReviewMarkers;
    }

    protected $fillable = [
        'slug',
        'title',
        'description',
        'summary',
        'year',
        'role',
        'image',
        'image_dark',
        'tags',
        'github_url',
        'demo_url',
        'featured',
        'sort_order',
        'problem',
        'solution',
        'result',
        'gallery',
        'gallery_dark',
        'video',
        'jebbari_url',
        'is_published',
        'status',
        'lessons',
        'seo_title',
        'seo_description',
        'image_alt',
        'gallery_alts',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'summary' => 'array',
        'role' => 'array',
        'year' => 'integer',
        'featured' => 'boolean',
        'sort_order' => 'integer',
        'problem' => 'array',
        'solution' => 'array',
        'result' => 'array',
        'gallery' => 'array',
        'gallery_dark' => 'array',
        'jebbari_url' => 'array',
        'is_published' => 'boolean',
        'lessons' => 'array',
        'seo_title' => 'array',
        'seo_description' => 'array',
        'image_alt' => 'array',
        'gallery_alts' => 'array',
    ];

    protected static function booted(): void
    {
        // Every project gets a stable, readable slug; numeric slugs are avoided so ids and slugs never collide.
        static::saving(function (Project $project) {
            if (blank($project->slug)) {
                $project->slug = static::uniqueSlug($project->title['nl'] ?? $project->title['en'] ?? 'project', $project->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;

        for ($i = 2; ctype_digit($slug) || static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** /project/{slug}, and the old /project/{id} (the controller then redirects with a 301). */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return static::where('slug', $value)->first()
            ?? (ctype_digit((string) $value) ? static::find((int) $value) : null);
    }

    public function getRouteKey(): mixed
    {
        return $this->slug ?: $this->getKey();
    }

    /** Only what guests may see. Drafts stay hidden from lists, sitemap, search and llms.txt. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isPublishedState(): bool
    {
        // New records without an explicit value get the column default (published).
        return $this->is_published ?? true;
    }

    public function reviewFields(): array
    {
        return ['title', 'summary', 'description', 'role', 'problem', 'solution', 'lessons', 'result', 'seo_title', 'seo_description', 'image_alt', 'gallery_alts', 'tags'];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('featured')->orderBy('sort_order')->orderByDesc('year')->latest();
    }

    public function testimonials(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /** Filled case sections (problem → what I built → hard parts → result) in the current language. */
    public function caseBlocks(): array
    {
        return collect(['problem', 'solution', 'lessons', 'result'])
            ->mapWithKeys(fn ($field) => [$field => trim((string) $this->getTranslation($field))])
            ->filter()
            ->all();
    }

    public function videoUrl(): ?string
    {
        return $this->video ? asset('storage/'.$this->video) : null;
    }

    /** @return list<string> light versions (or the dark one when there is no light one) */
    public function galleryUrls(): array
    {
        return array_column($this->galleryItems(), 'light');
    }

    /**
     * Gallery images with a version per theme. A missing version falls back to the other one,
     * so 'light' and 'dark' are always set. Items with neither are skipped.
     *
     * @return list<array{light: string, dark: string, alt: string}>
     */
    public function galleryItems(): array
    {
        $light = array_values($this->gallery ?? []);
        $dark = array_values($this->gallery_dark ?? []);
        $items = [];

        for ($i = 0, $n = max(count($light), count($dark)); $i < $n; $i++) {
            $l = ($light[$i] ?? null) ?: null;
            $d = ($dark[$i] ?? null) ?: null;
            if (! $l && ! $d) {
                continue;
            }
            $items[] = [
                'light' => asset('storage/'.($l ?? $d)),
                'dark' => asset('storage/'.($d ?? $l)),
                'alt' => $this->galleryAlt($i),
            ];
        }

        if (! $items) {
            foreach ($this->legacyServerScopeShots() as $i => $url) {
                $items[] = ['light' => $url, 'dark' => $url, 'alt' => $this->galleryAlt($i)];
            }
        }

        return $items;
    }

    /**
     * ServerScope screenshots uploaded before ServerScope became a project (Filament "Foto's & media", ss-* slots).
     *
     * @return list<string>
     */
    private function legacyServerScopeShots(): array
    {
        if ($this->slug !== 'serverscope') {
            return [];
        }

        return collect(array_keys(trans('messages.serverscope.features', [], 'nl')))
            ->map(fn ($key) => Setting::media("ss-{$key}"))
            ->filter()->values()->all();
    }

    public function getTranslation($field)
    {
        $locale = App::getLocale();
        $value = $this->$field;

        if (is_array($value)) {
            return $value[$locale]
                ?? $value['en']
                ?? $value['nl']
                ?? '';
        }

        return $value ?? '';
    }

    public function tagList(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->tags))));
    }

    public function excerpt(int $limit = 160): string
    {
        return Str::limit(preg_replace('/\s+/', ' ', strip_tags($this->getTranslation('description'))), $limit);
    }

    /** The one-liner for rows and chapters: the summary if set, else a short excerpt of the description. */
    public function oneLiner(): string
    {
        return trim((string) $this->getTranslation('summary')) ?: $this->excerpt(110);
    }

    /**
     * The uploaded screenshot, or null. There are deliberately no stock fallbacks any more:
     * a project without a real screenshot shows a neutral typographic panel instead.
     */
    public function coverUrl(): ?string
    {
        $path = $this->image ?: $this->image_dark;

        return $path ? asset('storage/'.$path) : ($this->legacyServerScopeShots()[0] ?? null);
    }

    /** The dark-theme cover: its own upload, else the light one (and the other way round in coverUrl()). */
    public function coverDarkUrl(): ?string
    {
        return $this->image_dark ? asset('storage/'.$this->image_dark) : $this->coverUrl();
    }

    /**
     * Filament edits the gallery as one list of rows (light, dark, alt NL, alt EN). These two helpers
     * convert between that list and the stored columns gallery / gallery_dark / gallery_alts.
     *
     * @return list<array{light: ?string, dark: ?string, alt_nl: string, alt_en: string}>
     */
    public static function galleryRows(array $data): array
    {
        $light = array_values($data['gallery'] ?? []);
        $dark = array_values($data['gallery_dark'] ?? []);
        $alts = array_values($data['gallery_alts'] ?? []);
        $rows = [];
        for ($i = 0, $n = max(count($light), count($dark)); $i < $n; $i++) {
            $rows[] = [
                'light' => ($light[$i] ?? null) ?: null,
                'dark' => ($dark[$i] ?? null) ?: null,
                'alt_nl' => (string) ($alts[$i]['nl'] ?? ''),
                'alt_en' => (string) ($alts[$i]['en'] ?? ''),
            ];
        }

        return $rows;
    }

    /** @return array{gallery: list<?string>, gallery_dark: list<?string>, gallery_alts: list<array{nl: string, en: string}>} */
    public static function galleryColumns(array $rows): array
    {
        $out = ['gallery' => [], 'gallery_dark' => [], 'gallery_alts' => []];
        foreach (array_values($rows) as $row) {
            $first = fn ($v) => is_array($v) ? (array_values($v)[0] ?? null) : $v; // FileUpload state can be ['uuid' => path]
            $light = $first($row['light'] ?? null) ?: null;
            $dark = $first($row['dark'] ?? null) ?: null;
            if (! $light && ! $dark) {
                continue;
            }
            $out['gallery'][] = $light;
            $out['gallery_dark'][] = $dark;
            $out['gallery_alts'][] = ['nl' => (string) ($row['alt_nl'] ?? ''), 'en' => (string) ($row['alt_en'] ?? '')];
        }
        $out['gallery_dark'] = array_filter($out['gallery_dark']) ? $out['gallery_dark'] : [];

        return $out;
    }

    /** Markers in the Filament form state count too (the gallery alts live in gallery_rows there). */
    public function reviewMarkers(?array $attributes = null): array
    {
        if ($attributes !== null && isset($attributes['gallery_rows'])) {
            // Every row's alt text counts, even a row whose image is still uploading or missing.
            $attributes['gallery_alts'] = array_values(array_map(
                fn ($row) => ['nl' => (string) ($row['alt_nl'] ?? ''), 'en' => (string) ($row['alt_en'] ?? '')],
                (array) $attributes['gallery_rows'],
            ));
        }

        return $this->traitReviewMarkers($attributes);
    }

    /** Alt text of the cover: the written one, else "Screenshot of {title}". */
    public function coverAlt(): string
    {
        return trim((string) $this->getTranslation('image_alt'))
            ?: __('site.work.screenshot_alt', ['name' => $this->getTranslation('title')]);
    }

    /** Alt text of gallery image $index in the current language. */
    public function galleryAlt(int $index): string
    {
        $alts = $this->gallery_alts[$index] ?? [];
        $locale = app()->getLocale();

        return trim((string) ($alts[$locale] ?? $alts['en'] ?? $alts['nl'] ?? ''))
            ?: __('site.case.image_n', ['name' => $this->getTranslation('title'), 'n' => $index + 1]);
    }

    public function host(): ?string
    {
        return $this->hasDemo() ? preg_replace('/^www\./', '', (string) parse_url($this->demo_url, PHP_URL_HOST)) : null;
    }

    /** The matching page on jebbari.nl for projects built with Jebbari, in the current language. */
    public function jebbariUrl(): ?string
    {
        $url = trim((string) $this->getTranslation('jebbari_url'));

        return str_starts_with($url, 'https://jebbari.nl') ? $url : null;
    }

    public function hasGithub(): bool
    {
        return filled($this->github_url)
            && ! in_array($this->github_url, ['https://github.com', 'https://github.com/'], true);
    }

    public function hasDemo(): bool
    {
        return filled($this->demo_url) && ! str_contains((string) $this->demo_url, 'example.com');
    }
}
