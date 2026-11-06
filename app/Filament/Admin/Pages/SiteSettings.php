<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/** Links shown on the public site. Values here override the matching .env settings. */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    /** Setting key => config key it overrides. */
    public const MAP = [
        'links.booking' => 'site.booking_url',
        'links.github' => 'site.github',
        'links.phone' => 'site.phone',
        'links.serverscope' => 'site.serverscope.url',
        'links.serverscope_docs' => 'site.serverscope.docs_url',
        'links.serverscope_status' => 'site.serverscope.status_url',
    ];

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Links & instellingen';

    protected static ?string $title = 'Links & instellingen';

    protected static string $view = 'filament.admin.pages.site-media';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(collect(self::MAP)->mapWithKeys(fn ($config, $key) => [str_replace('.', '_', $key) => Setting::get($key)])->all());
    }

    public function form(Form $form): Form
    {
        $url = fn (string $name, string $label) => TextInput::make($name)->label($label)->url()->maxLength(255);

        return $form
            ->statePath('data')
            ->schema([
                Section::make('Contact')
                    ->description('Een afsprakenlink (bijv. Cal.com of Calendly) toont een knop "Plan een gesprek" in de hero en bij contact.')
                    ->aside()
                    ->schema([
                        $url('links_booking', 'Afsprakenlink')->placeholder('https://cal.com/jouw-naam/kennismaking'),
                        TextInput::make('links_phone')->label('Telefoonnummer')->tel()->placeholder('+31 6 …')
                            ->helperText('Leeg = geen telefoonnummer op de site.'),
                        $url('links_github', 'GitHub-profiel')->placeholder('https://github.com/…'),
                    ]),
                Section::make('ServerScope')
                    ->description('De statuspagina-URL toont de officiële ServerScope-badge op je site, met een link naar je publieke statuspagina.')
                    ->aside()
                    ->schema([
                        $url('links_serverscope', 'ServerScope-website')->placeholder('https://serverscope.nl'),
                        $url('links_serverscope_docs', 'Documentatie')->placeholder('https://info.serverscope.nl'),
                        $url('links_serverscope_status', 'Publieke statuspagina')->placeholder('https://…/status/jouw-slug')
                            ->rule('regex:#/status/[^/]+/?$#')
                            ->validationMessages(['regex' => 'Gebruik de URL van een publieke statuspagina, eindigend op /status/<slug>.']),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [Action::make('save')->label('Opslaan')->submit('save')->keyBindings(['mod+s'])];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::put(collect(self::MAP)->mapWithKeys(fn ($config, $key) => [$key => $state[str_replace('.', '_', $key)] ?: null])->all());

        Notification::make()->title('Opgeslagen')->success()->send();
    }

    /** Apply saved values on top of config/site.php (called on boot). */
    public static function applyToConfig(): void
    {
        foreach (self::MAP as $key => $config) {
            if ($value = Setting::get($key)) {
                config([$config => $value]);
            }
        }

        if (Setting::get('links.phone')) {
            config(['site.phone_href' => preg_replace('/[^+0-9]/', '', Setting::get('links.phone'))]);
        }
    }
}
