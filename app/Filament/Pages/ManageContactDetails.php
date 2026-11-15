<?php

namespace App\Filament\Pages;

use App\Filament\Support\Translatable;
use App\Support\ContactDetails;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

/**
 * Edit the email, phone, location and social links shown on the site.
 * The email also receives the messages sent from the contact form.
 */
class ManageContactDetails extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Profile';

    protected static ?string $navigationLabel = 'Contact details';

    protected static ?string $title = 'Contact details';

    protected static ?string $slug = 'contact-details';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $details = ContactDetails::all();

        $this->form->fill([
            'email' => $details['email'],
            'phone' => $details['phone'],
            'location' => $details['location'],
            'socials' => array_replace(array_fill_keys(array_keys(ContactDetails::SOCIALS), null), $details['socials']),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Email and phone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->rules(ContactDetails::emailRules())
                            ->validationMessages(['email' => 'Enter a valid email address, e.g. name@example.com.'])
                            ->prefixIcon(Heroicon::OutlinedEnvelope)
                            ->helperText('Shown on the site. Messages from the contact form are delivered here.'),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(30)
                            ->rule('regex:/^\+?[0-9][0-9 ().-]{5,28}$/')
                            ->validationMessages(['regex' => 'Use digits, spaces and + ( ) - only, e.g. +212 600 000 000.'])
                            ->prefixIcon(Heroicon::OutlinedPhone)
                            ->extraInputAttributes(['dir' => 'ltr'])
                            ->helperText('Include the country code. Leave empty to hide it.'),
                    ]),

                Section::make('Location')
                    ->description('French and Arabic fall back to English when empty.')
                    ->schema([
                        Translatable::tabs('Location', fn (string $locale) => [
                            TextInput::make("location.$locale")
                                ->label('City, country')
                                ->maxLength(100)
                                ->required(Translatable::required($locale))
                                ->extraInputAttributes($locale === 'ar' ? ['dir' => 'rtl'] : []),
                        ]),
                    ]),

                Section::make('Social links')
                    ->description('Full https links to your profiles. Leave a field empty to hide that icon.')
                    ->columns(2)
                    ->schema(array_map(
                        fn (string $network, array $social) => TextInput::make("socials.$network")
                            ->label($social['label'])
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://'.$social['domains'][0].'/your-name')
                            ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail) use ($network, $social) {
                                if (filled($value) && ! ContactDetails::isValidSocialUrl($network, $value)) {
                                    $fail('Use an https link on '.implode(' or ', $social['domains']).'.');
                                }
                            }),
                        array_keys(ContactDetails::SOCIALS),
                        ContactDetails::SOCIALS,
                    )),
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
                    ]),
                ]),
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTest')
                ->label('Send test email')
                ->color('gray')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->requiresConfirmation()
                ->modalDescription(fn (): string => 'Sends a test message to '.ContactDetails::recipient().', the saved address that receives contact-form messages.')
                ->action(fn () => $this->sendTestEmail()),
        ];
    }

    public function save(): void
    {
        ContactDetails::save($this->form->getState());

        Notification::make()->success()->title('Contact details saved')->send();
    }

    public function sendTestEmail(): void
    {
        $recipient = ContactDetails::recipient();

        try {
            Mail::raw(
                "This is a test from your website's admin panel.\n\n"
                ."Messages sent from the contact form on ".config('app.url')." will arrive at this address.",
                fn ($message) => $message->to($recipient)->subject('Test: contact form delivery'),
            );
        } catch (Throwable $e) {
            report($e);

            Notification::make()->danger()
                ->title('The test email could not be sent')
                ->body('Check the MAIL_* settings in .env. Error: '.$e->getMessage())
                ->send();

            return;
        }

        Notification::make()->success()
            ->title('Test email sent to '.$recipient)
            ->body(config('mail.default') === 'log'
                ? 'MAIL_MAILER is "log": the email was written to storage/logs/laravel.log instead of being delivered.'
                : 'Check that inbox (and the spam folder).')
            ->send();
    }
}
