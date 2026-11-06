<?php

namespace App\Filament\Admin\Pages;

use App\Http\Middleware\RequireTwoFactor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

/** Set up and manage the mandatory two-factor authentication for the admin. */
class TwoFactor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationGroup = 'Beveiliging';

    protected static ?string $navigationLabel = 'Tweestapsverificatie';

    protected static ?string $title = 'Tweestapsverificatie';

    protected static ?string $slug = 'two-factor';

    protected static string $view = 'filament.admin.pages.two-factor';

    public string $code = '';

    public bool $showRecoveryCodes = false;

    public function getSubheading(): ?string
    {
        return auth()->user()->hasConfirmedTwoFactor()
            ? 'Actief. Bij elke nieuwe login vraagt het beheer om een code uit je authenticator-app.'
            : 'Verplicht voor het beheer. Stel het nu in met een app als Google Authenticator, 1Password of Authy.';
    }

    public function start(EnableTwoFactorAuthentication $enable): void
    {
        $enable(auth()->user());
        $this->code = '';
    }

    public function confirm(ConfirmTwoFactorAuthentication $confirm): void
    {
        $this->validate(['code' => ['required', 'digits:6']], [], ['code' => 'code']);

        try {
            $confirm(auth()->user(), $this->code);
        } catch (\Illuminate\Validation\ValidationException) {
            $this->addError('code', 'Deze code klopt niet. Controleer of de tijd op je telefoon goed staat en probeer de volgende code.');

            return;
        }

        session()->put(RequireTwoFactor::SESSION_KEY, auth()->id());
        $this->showRecoveryCodes = true;

        Notification::make()->title('Tweestapsverificatie staat aan')->body('Bewaar je herstelcodes op een veilige plek.')->success()->send();
    }

    public function regenerate(GenerateNewRecoveryCodes $generate): void
    {
        $generate(auth()->user());
        $this->showRecoveryCodes = true;

        Notification::make()->title('Nieuwe herstelcodes gemaakt')->body('De oude codes werken niet meer.')->warning()->send();
    }

    protected function getViewData(): array
    {
        $user = auth()->user()->fresh();

        return [
            'user' => $user,
            'pending' => filled($user->two_factor_secret) && ! $user->hasConfirmedTwoFactor(),
            'qr' => filled($user->two_factor_secret) && ! $user->hasConfirmedTwoFactor() ? $user->twoFactorQrCodeSvg() : null,
            'secret' => filled($user->two_factor_secret) && ! $user->hasConfirmedTwoFactor() ? decrypt($user->two_factor_secret) : null,
            'codes' => $this->showRecoveryCodes ? $user->recoveryCodes() : [],
        ];
    }
}
