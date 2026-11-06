<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/** Emergency exit when the phone with the authenticator app is lost. Run over SSH only. */
class ResetTwoFactor extends Command
{
    protected $signature = 'admin:reset-2fa {email}';

    protected $description = 'Turn off two-factor authentication for a user, so it can be set up again at the next login';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('User not found.');

            return self::FAILURE;
        }

        $user->forceFill(['two_factor_secret' => null, 'two_factor_recovery_codes' => null, 'two_factor_confirmed_at' => null])->save();
        $this->info("2FA reset for {$user->email}. Set it up again after logging in.");

        return self::SUCCESS;
    }
}
