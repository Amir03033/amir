<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Question;
use App\Support\Voter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** "Ask Amir": a public page with answered questions, and the endpoint that stores new ones. */
class QuestionController extends Controller
{
    public function index()
    {
        return view('ask', ['questions' => Question::published()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        // Honeypot: real visitors never fill this. Pretend it worked so bots learn nothing.
        if (filled($request->input('website'))) {
            return response()->json(['success' => true, 'message' => __('site.ask.success')]);
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:190'],
            'body' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        Question::create([
            ...$data,
            'body' => trim($data['body']),
            'locale' => app()->getLocale(),
            'voter_hash' => Voter::hash($request),
        ]);
        Event::record('question_submit');

        return response()->json(['success' => true, 'message' => __('site.ask.success')]);
    }
}
