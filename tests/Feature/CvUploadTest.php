<?php

use App\Filament\Pages\ManageCv;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    config(['portfolio.admin_emails' => ['owner@example.com']]);
    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']));
});

it('opens the CV page', function () {
    $this->get('/admin/cv')->assertOk()->assertSee('Downloadable CV');
});

it('links the default CV until one is uploaded', function () {
    $this->get('/')->assertSee(asset(config('portfolio.profile.cv')), false);
});

it('uploads a CV and the site links to it', function () {
    Livewire::test(ManageCv::class)
        ->set('data.cv', UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf'))
        ->call('save')
        ->assertHasNoFormErrors();

    $path = Setting::get(Setting::CV_PATH);
    expect($path)->toStartWith('cv/oussama-driouech-cv-')->toEndWith('.pdf');
    Storage::disk('public')->assertExists($path);

    $this->get('/')->assertSee(Storage::disk('public')->url($path), false);
});

it('deletes the old upload when replaced, and falls back to the default when removed', function () {
    Livewire::test(ManageCv::class)
        ->set('data.cv', UploadedFile::fake()->create('one.pdf', 100, 'application/pdf'))
        ->call('save');
    $first = Setting::get(Setting::CV_PATH);

    // Like the UI: remove the current file (×), then drop the new one.
    Livewire::test(ManageCv::class)
        ->set('data.cv', null)
        ->set('data.cv', [UploadedFile::fake()->create('two.pdf', 100, 'application/pdf')])
        ->call('save');
    $second = Setting::get(Setting::CV_PATH);

    expect($second)->not->toBe($first);
    Storage::disk('public')->assertMissing($first);

    Livewire::test(ManageCv::class)->set('data.cv', null)->call('save');

    expect(Setting::get(Setting::CV_PATH))->toBeNull();
    Storage::disk('public')->assertMissing($second);
    $this->get('/')->assertSee(asset(config('portfolio.profile.cv')), false);
});

it('rejects files that are not PDFs', function () {
    Livewire::test(ManageCv::class)
        ->set('data.cv', UploadedFile::fake()->create('photo.png', 50, 'image/png'))
        ->call('save')
        ->assertHasFormErrors(['cv']);

    expect(Setting::get(Setting::CV_PATH))->toBeNull();
});
