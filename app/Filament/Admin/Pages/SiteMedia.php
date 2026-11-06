<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

/** Every image on the public site that is not a project or blog image. */
class SiteMedia extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = "Foto's & media";

    protected static ?string $title = "Foto's & media";

    protected static string $view = 'filament.admin.pages.site-media';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(collect($this->keys())->mapWithKeys(fn ($key) => [$key => Setting::get("media.{$key}")])->all());
    }

    /** @return list<string> */
    private function keys(): array
    {
        return ['avatar', 'og', ...array_map(fn ($k) => "ss-{$k}", array_keys(__('messages.serverscope.features', [], 'nl')))];
    }

    private function upload(string $key, string $label): FileUpload
    {
        return FileUpload::make($key)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory('site')
            ->visibility('public')
            ->imageEditor()
            ->maxSize(8192);
    }

    public function form(Form $form): Form
    {
        $features = __('messages.serverscope.features', [], 'nl');

        return $form
            ->statePath('data')
            ->schema([
                Section::make('Profielfoto')
                    ->description('Kleine ronde foto in de hero en bij "Over mij". Wordt vierkant bijgesneden en verkleind naar 400×400.')
                    ->aside()
                    ->schema([
                        $this->upload('avatar', 'Profielfoto')
                            ->avatar()
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400'),
                    ]),

                Section::make('Deelafbeelding')
                    ->description('Wordt getoond als iemand je site deelt op LinkedIn, WhatsApp, enz. Formaat 1200×630.')
                    ->aside()
                    ->schema([
                        $this->upload('og', 'Open Graph-afbeelding')
                            ->imageCropAspectRatio('1200:630')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('630'),
                    ]),

                Section::make('ServerScope screenshots')
                    ->description('Eén screenshot per feature in de ServerScope-case. Zolang er geen is, staat er "Screenshot volgt". Wordt verkleind naar max. 1600px breed.')
                    ->schema(collect($features)->map(fn ($feature, $key) => $this->upload("ss-{$key}", $feature['title'])
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('1600')
                        ->imageResizeTargetHeight('1000')
                    )->values()->all())
                    ->columns(2),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Opslaan')->submit('save')->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($this->keys() as $key) {
            $old = Setting::get("media.{$key}");
            $new = data_get($state, $key);

            // Remove the previous file when an image is replaced or cleared.
            if ($old && $old !== $new) {
                Storage::disk('public')->delete($old);
            }

            Setting::put(["media.{$key}" => $new]);
        }

        Notification::make()->title('Opgeslagen')->success()->send();
    }
}
