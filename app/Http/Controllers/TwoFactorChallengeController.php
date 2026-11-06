<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RequireTwoFactor;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

/** Second step after the Filament login: a TOTP code or a one-time recovery code. */
class TwoFactorChallengeController extends Controller
{
    public function show(Request $request)
    {
        if (! $request->user()->hasConfirmedTwoFactor()) {
            return redirect()->to(url(filament()->getPanel('Admin')->getPath()));
        }

        return view('auth.two-factor-check');
    }

    public function verify(Request $request, TwoFactorAuthenticationProvider $provider)
    {
        $user = $request->user();
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:10'],
            'recovery_code' => ['nullable', 'string', 'max:64'],
        ]);

        $valid = false;

        if (filled($data['code'] ?? null)) {
            $valid = $provider->verify(decrypt($user->two_factor_secret), preg_replace('/\D/', '', $data['code']));
        } elseif (filled($data['recovery_code'] ?? null)) {
            $code = collect($user->recoveryCodes())->first(fn ($c) => hash_equals($c, trim($data['recovery_code'])));
            if ($code) {
                $user->replaceRecoveryCode($code);
                $valid = true;
            }
        }

        if (! $valid) {
            return back()->withErrors(['code' => __('messages.two_factor.invalid')]);
        }

        $request->session()->regenerate();
        $request->session()->put(RequireTwoFactor::SESSION_KEY, $user->getKey());

        return redirect()->intended(url(filament()->getPanel('Admin')->getPath()));
    }
}
