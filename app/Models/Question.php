<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** A visitor's question for "Ask Amir". Only answered questions marked public are ever shown on the site. */
class Question extends Model
{
    protected $fillable = ['name', 'email', 'body', 'locale', 'answer', 'answered_at', 'is_public', 'voter_hash'];

    protected $hidden = ['email', 'voter_hash'];

    protected $casts = [
        'answered_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Answering sets the date once; clearing the answer also takes it off the public site.
        static::saving(function (Question $q) {
            if (filled($q->answer) && ! $q->answered_at) {
                $q->answered_at = now();
            }
            if (blank($q->answer)) {
                $q->answered_at = null;
                $q->is_public = false;
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_public', true)->whereNotNull('answer')->whereNotNull('answered_at')->latest('answered_at');
    }

    public function isAnswered(): bool
    {
        return filled($this->answer);
    }
}
