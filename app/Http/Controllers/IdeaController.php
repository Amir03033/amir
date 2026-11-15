<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Idea;
use App\Models\IdeaVote;
use App\Support\Voter;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** "Suggest something": the public roadmap, new ideas (hidden until approved) and anonymous upvotes. */
class IdeaController extends Controller
{
    public function index(Request $request)
    {
        $ideas = Idea::published()->ranked()->get();

        return view('roadmap', [
            'groups' => collect(Idea::STATUSES)->mapWithKeys(fn ($s) => [$s => $ideas->where('status', $s)->values()]),
            'voted' => self::votedIds($request),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (filled($request->input('website'))) {
            return response()->json(['success' => true, 'message' => __('site.ideas.success')]);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'min:4', 'max:140'],
            'detail' => ['nullable', 'string', 'max:1000'],
        ]);

        Idea::create([
            'title' => trim($data['title']),
            'body' => filled($data['detail'] ?? null) ? trim($data['detail']) : null,
            'status' => 'new',
            'is_public' => false,
            'locale' => app()->getLocale(),
            'voter_hash' => Voter::hash($request),
        ]);
        Event::record('idea_submit');

        return response()->json(['success' => true, 'message' => __('site.ideas.success')]);
    }

    /** One vote per idea per visitor: both the IP hash and the session hash must be new for this idea. */
    public function vote(Request $request, Idea $idea): JsonResponse
    {
        abort_unless($idea->is_public && $idea->isVotable(), 404);

        $voter = Voter::hash($request);
        $session = Voter::session($request);

        $already = $idea->votes()->where(fn ($q) => $q->where('voter_hash', $voter)->orWhere('session_hash', $session))->exists();

        if (! $already) {
            try {
                DB::transaction(function () use ($idea, $voter, $session) {
                    IdeaVote::create(['idea_id' => $idea->id, 'voter_hash' => $voter, 'session_hash' => $session]);
                    $idea->increment('votes_count');
                });
                Event::record('idea_vote');
            } catch (UniqueConstraintViolationException) {
                // A double click raced us; the first vote counts.
            }
        }

        return response()->json([
            'success' => true,
            'voted' => true,
            'votes' => $idea->fresh()->votes_count,
            'message' => $already ? __('site.ideas.already') : __('site.ideas.voted'),
        ]);
    }

    /** @return list<int> ids of ideas this visitor already voted on */
    public static function votedIds(Request $request): array
    {
        return IdeaVote::where('voter_hash', Voter::hash($request))
            ->orWhere('session_hash', Voter::session($request))
            ->pluck('idea_id')->unique()->values()->all();
    }
}
