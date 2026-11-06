<?php

namespace App\Http\Middleware;

use App\Filament\Admin\Pages\TwoFactor;
use Closure;
use Illuminate\Http\Request;

/**
 * 2FA is mandatory for the admin panel:
 * - no 2FA yet → only the setup page is reachable;
 * - 2FA set up → a code is required once per session (see TwoFactorChallengeController).
 */
class RequireTwoFactor
{
    public const SESSION_KEY = 'two_factor_passed';

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $onSetupPage = $request->routeIs(TwoFactor::getRouteName());

        if (! $user->hasConfirmedTwoFactor()) {
            return $onSetupPage ? $next($request) : redirect()->to(TwoFactor::getUrl());
        }

        if ($request->session()->get(self::SESSION_KEY) !== $user->getKey()) {
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('two-factor.check');
        }

        return $next($request);
    }
}
