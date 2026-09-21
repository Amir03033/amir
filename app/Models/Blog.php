<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'image',
        'meta_title',
        'meta_description',
        'status',
        'published_at',
        'category',
        'tags',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
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

    public function excerpt(int $limit = 180): string
    {
        return Str::limit(
            preg_replace(
                '/\s+/',
                ' ',
                strip_tags(
                    Str::markdown(
                        $this->getTranslation('content')
                    )
                )
            ),
            $limit
        );
    }

    public function isPublished(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if (
            $this->published_at !== null
            && $this->published_at->isFuture()
        ) {
            return false;
        }

        return true;
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled'
            && $this->published_at !== null
            && $this->published_at->isFuture();
    }

    public function tagList(): array
    {
        return array_values(
            array_filter(
                array_map(
                    'trim',
                    $this->tags ?? []
                )
            )
        );
    }
}