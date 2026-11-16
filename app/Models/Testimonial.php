<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

class Testimonial extends Model
{
    use \App\Models\Concerns\HasReviewMarkers;

    protected $fillable = ['name', 'role', 'company', 'quote', 'avatar', 'url', 'project_id', 'is_visible', 'sort_order'];

    protected $casts = [
        'quote' => 'array',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** @return list<string> */
    public function reviewFields(): array
    {
        return ['name', 'role', 'company', 'quote'];
    }

    /** "Published" for a review means: shown on the site. */
    public function isPublishedState(): bool
    {
        return (bool) $this->is_visible;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true)->orderBy('sort_order');
    }

    public function getTranslation($field)
    {
        $value = $this->$field;

        if (is_array($value)) {
            return $value[App::getLocale()] ?? $value['en'] ?? $value['nl'] ?? '';
        }

        return $value ?? '';
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    }
}
