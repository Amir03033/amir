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
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'featured' => 'boolean',
        'sort_order' => 'integer',
    ];

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
            'klus' => 'images/projects/klusklaar.png',
            'jungle' => 'images/projects/jungletuin.jpg',
            'master' => 'images/projects/mastermind.jpg',
            'mind' => 'images/projects/mastermind.jpg',
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