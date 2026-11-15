<?php

use App\Filament\Pages\ManageContactDetails;
use App\Mail\NewContactMessage;
use App\Models\User;
use App\Support\ContactDetails;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    config(['portfolio.admin_emails' => ['owner@example.com']]);
    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']));
});

function validDetails(array $overrides = []): array
{
    return array_replace_recursive([
        'email' => 'new@example.com',
        'phone' => '+212 611 223 344',
        'location' => ['en' => 'Casablanca, Morocco', 'fr' => '', 'ar' => 'الدار البيضاء، المغرب'],
        'socials' => [
            'linkedin' => 'https://www.linkedin.com/in/someone',
            'instagram' => '',
            'x' => 'https://twitter.com/someone',
            'github' => 'https://github.com/someone',
        ],
    ], $overrides);
}

it('opens the page filled with the current details', function () {
    $this->get('/admin/contact-details')->assertOk()->assertSee('Social links');

    Livewire::test(ManageContactDetails::class)
        ->assertSet('data.email', config('portfolio.profile.email'))
        ->assertSet('data.socials.github', config('portfolio.profile.socials.github'));
});

it('saves the details and the site shows them', function () {
    Livewire::test(ManageContactDetails::class)
        ->fillForm(validDetails())
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/contact')
        ->assertSee('new@example.com')
        ->assertSee('+212 611 223 344')
        ->assertSee('Casablanca, Morocco');

    // French falls back to English; Arabic uses its own text.
    $this->get('/fr/contact')->assertSee('Casablanca, Morocco');
    $this->get('/ar/contact')->assertSee('الدار البيضاء، المغرب');

    // Empty social links are hidden; the others are linked.
    $data = $this->get('/')->viewData('appData')['profile'];
    expect($data['socials'])->toBe([
        'linkedin' => 'https://www.linkedin.com/in/someone',
        'x' => 'https://twitter.com/someone',
        'github' => 'https://github.com/someone',
    ]);
    $this->get('/')->assertSee('href="https://github.com/someone"', false)
        ->assertDontSee('aria-label="Instagram"', false);
});

it('hides the phone when it is left empty', function () {
    Livewire::test(ManageContactDetails::class)
        ->fillForm(validDetails(['phone' => '']))
        ->call('save')
        ->assertHasNoFormErrors();

    $this->get('/contact')->assertDontSee('data-profile="phone"', false);
});

it('delivers contact form messages to the saved email', function () {
    Mail::fake();
    ContactDetails::save(validDetails(['email' => 'inbox@example.com']));

    $this->post('/contact', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'project',
        'message' => 'Hello, I would like to talk about a new website.',
    ])->assertRedirect();

    Mail::assertSent(NewContactMessage::class, fn ($mail) => $mail->hasTo('inbox@example.com'));
});

it('rejects invalid details', function (array $overrides, string $field) {
    Livewire::test(ManageContactDetails::class)
        ->fillForm(validDetails($overrides))
        ->call('save')
        ->assertHasFormErrors([$field]);
})->with([
    'no email' => [['email' => ''], 'email'],
    'malformed email' => [['email' => 'not-an-email'], 'email'],
    'email with spaces' => [['email' => 'me @example.com'], 'email'],
    'letters in phone' => [['phone' => 'call me'], 'phone'],
    'no English location' => [['location' => ['en' => '']], 'location.en'],
    'http social link' => [['socials' => ['github' => 'http://github.com/someone']], 'socials.github'],
    'wrong site for network' => [['socials' => ['linkedin' => 'https://example.com/me']], 'socials.linkedin'],
    'look-alike domain' => [['socials' => ['github' => 'https://github.com.evil.io/me']], 'socials.github'],
]);

it('rejects an email whose domain cannot receive mail', function () {
    config(['portfolio.contact.verify_email_dns' => true]);

    // The .invalid top-level domain can never exist (RFC 2606).
    Livewire::test(ManageContactDetails::class)
        ->fillForm(validDetails(['email' => 'me@no-such-domain.invalid']))
        ->call('save')
        ->assertHasFormErrors(['email']);
});

it('sends a test email to the saved address', function () {
    Mail::fake();
    ContactDetails::save(validDetails(['email' => 'inbox@example.com']));

    Livewire::test(ManageContactDetails::class)
        ->callAction('sendTest')
        ->assertNotified('Test email sent to inbox@example.com');
});
