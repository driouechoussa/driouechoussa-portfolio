<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

/**
 * Upload the CV that the site's "Download CV" buttons point to.
 * With no upload, the site uses the file in config('portfolio.profile.cv').
 */
class ManageCv extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Profile';

    protected static ?string $navigationLabel = 'CV';

    protected static ?string $title = 'CV';

    protected static ?string $slug = 'cv';

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['cv' => Setting::get(Setting::CV_PATH)]);
    }

    public function form(Schema $schema): Schema
    {
        $default = config('portfolio.profile.cv');

        return $schema
            ->statePath('data')
            ->components([
                Section::make('Downloadable CV')
                    ->description('The PDF visitors get from the "Download CV" buttons on every page.')
                    ->schema([
                        FileUpload::make('cv')
                            ->label('PDF file')
                            ->disk('public')
                            ->directory('cv')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10 * 1024)
                            ->downloadable()
                            ->openable()
                            // Dated name: a new upload gets a new URL, so browsers never serve the old file.
                            ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file): string => Str::slug(config('portfolio.profile.name.en')).'-cv-'.now()->format('Y-m-d').'-'.Str::lower(Str::random(6)).'.pdf')
                            ->helperText(new HtmlString(
                                'PDF, up to 10 MB. Remove the file and save to go back to the default CV '
                                .'(<a href="'.e(Media::url($default)).'" target="_blank" class="underline">'.e(basename($default)).'</a>).'
                            )),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save')->submit('save')->keyBindings(['mod+s']),
                        Action::make('view')
                            ->label('View current CV')
                            ->color('gray')
                            ->url(fn (): ?string => Media::url(Setting::get(Setting::CV_PATH) ?: config('portfolio.profile.cv')))
                            ->openUrlInNewTab(),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $path = $this->form->getState()['cv'] ?? null;
        $previous = Setting::get(Setting::CV_PATH);

        Setting::set(Setting::CV_PATH, $path);

        // Remove the replaced upload; the bundled default CV is never on this disk.
        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        Notification::make()
            ->success()
            ->title($path ? 'CV updated' : 'Upload removed. The site now uses the default CV.')
            ->send();
    }
}
