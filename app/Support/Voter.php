<?php

namespace App\Support;

use Illuminate\Http\Request;

/** Anonymous visitor fingerprints for votes and rate checks: one-way hashes, never the raw IP or session id. */
class Voter
{
    public static function hash(Request $request): string
    {
        return hash('sha256', 'voter|'.$request->ip().'|'.config('app.key'));
    }

    public static function session(Request $request): string
    {
        return hash('sha256', 'session|'.$request->session()->getId().'|'.config('app.key'));
    }
}
