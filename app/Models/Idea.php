<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A visitor's suggestion. Hidden until approved (is_public); its status places it on the public roadmap. */
class Idea extends Model
{
    public const STATUSES = ['new', 'planned', 'building', 'shipped', 'declined'];

    /** Statuses that can still receive votes. */
    public const VOTABLE = ['new', 'planned'];

    protected $fillable = ['title', 'body', 'status', 'is_public', 'votes_count', 'locale', 'voter_hash', 'sort_order'];

    protected $hidden = ['voter_hash'];

    protected $casts = [
        'is_public' => 'boolean',
        'votes_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(IdeaVote::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeRanked(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('votes_count')->latest();
    }

    public function isVotable(): bool
    {
        return in_array($this->status, self::VOTABLE, true);
    }
}
