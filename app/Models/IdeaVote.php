<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One upvote: identified by a hash of IP + app key, and a hash of the session id. Nothing personal is stored. */
class IdeaVote extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['idea_id', 'voter_hash', 'session_hash'];

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }
}
