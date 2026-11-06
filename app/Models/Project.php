<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
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
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'featured' => 'boolean',
        'sort_order' => 'integer',
        'problem' => 'array',
        'solution' => 'array',
        'result' => 'array',
        'gallery' => 'array',
    ];

    public function testimonials(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /** Filled case sections (problem → solution → result) in the current language. */
    public function caseBlocks(): array
    {
        return collect(['problem', 'solution', 'result'])
            ->mapWithKeys(fn ($field) => [$field => trim((string) $this->getTranslation($field))])
            ->filter()
            ->all();
    }

    /** @return list<string> */
    public function galleryUrls(): array
    {
        return collect($this->gallery ?? [])->filter()->map(fn ($path) => asset('storage/'.$path))->values()->all();
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
        return array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(',', (string) $this->tags)
                )
            )
        );
    }

    public function excerpt(int $limit = 160): string
    {
        return Str::limit(
            preg_replace(
                '/\s+/',
                ' ',
                strip_tags(
                    $this->getTranslation('description')
                )
            ),
            $limit
        );
    }

    public function coverUrl(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        $haystack = Str::slug(
            $this->getTranslation('title')
            . ' '
            . ($this->title['nl'] ?? '')
            . ' '
            . ($this->title['en'] ?? '')
        );

        $fallbacks = [
            'klus' => 'images/projects/klusklaar.webp',
            'jungle' => 'images/projects/jungletuin.webp',
            'master' => 'images/projects/mastermind.webp',
            'mind' => 'images/projects/mastermind.webp',
        ];

        foreach ($fallbacks as $needle => $path) {
            if (str_contains($haystack, $needle)) {
                return asset($path);
            }
        }

        return asset('images/projects/placeholder.svg');
    }

    public function hasGithub(): bool
    {
        return filled($this->github_url)
            && ! in_array(
                $this->github_url,
                [
                    'https://github.com',
                    'https://github.com/',
                ],
                true
            );
    }

    public function hasDemo(): bool
    {
        return filled($this->demo_url)
            && ! str_contains(
                (string) $this->demo_url,
                'example.com'
            );
    }
}