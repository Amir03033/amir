<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Project extends Model
{
    use \App\Models\Concerns\HasReviewMarkers;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'summary',
        'year',
        'role',
        'image',
        'tags',
        'github_url',
        'demo_url',
        'featured',
        'sort_order',
        'problem',
        'solution',
        'result',
        'gallery',
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

    /** @return list<string> */
    public function galleryUrls(): array
    {
        $own = collect($this->gallery ?? [])->filter()->map(fn ($path) => asset('storage/'.$path))->values()->all();

        return $own ?: $this->legacyServerScopeShots();
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
        return $this->image ? asset('storage/'.$this->image) : ($this->legacyServerScopeShots()[0] ?? null);
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
