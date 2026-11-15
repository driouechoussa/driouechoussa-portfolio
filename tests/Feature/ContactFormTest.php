<?php

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

function validMessage(array $overrides = []): array
{
    return array_merge([
        'name' => 'Jane Client',
        'email' => 'jane@example.com',
        'subject' => 'project',
        'message' => 'I would like a new website for my business, can we talk?',
    ], $overrides);
}

it('stores the message and emails the owner', function () {
    $this->postJson('/contact', validMessage())
        ->assertOk()
        ->assertJson(['message' => __('ui.form.success')]);

    $message = ContactMessage::sole();
    expect($message->email)->toBe('jane@example.com')
        ->and($message->locale)->toBe('en')
        ->and($message->read_at)->toBeNull();

    Mail::assertSent(NewContactMessage::class, fn ($mail) => $mail->hasTo(config('portfolio.profile.email'))
        && $mail->hasReplyTo('jane@example.com'));
});

it('records the language of the page the visitor used', function () {
    $this->postJson('/ar/contact', validMessage())->assertOk();

    expect(ContactMessage::sole()->locale)->toBe('ar');
});

it('validates the fields', function () {
    $this->postJson('/contact', validMessage(['email' => 'not-an-email', 'message' => 'too short', 'subject' => 'spam']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'message', 'subject']);

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingSent();
});

it('silently drops messages that fill the honeypot', function () {
    $this->postJson('/contact', validMessage(['website' => 'http://spam.example']))->assertOk();

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingSent();
});

it('rate limits the form', function () {
    foreach (range(1, 3) as $i) {
        $this->postJson('/contact', validMessage())->assertOk();
    }

    $this->postJson('/contact', validMessage())->assertTooManyRequests();
});

it('works without JavaScript (redirect with a status message)', function () {
    $this->post('/fr/contact', validMessage())
        ->assertRedirect(url('/fr/contact'))
        ->assertSessionHas('status', __('ui.form.success', [], 'fr'));
});

it('keeps the message when the email fails', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down'));

    $this->postJson('/contact', validMessage())->assertOk();

    expect(ContactMessage::count())->toBe(1);
});
