<?php

use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Livewire\Livewire;

beforeEach(function () {
    config(['portfolio.admin_emails' => ['owner@example.com']]);
    $this->seed(PortfolioSeeder::class);
});

function admin(): User
{
    return User::factory()->create(['email' => 'owner@example.com']);
}

it('sends guests to the login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('only lets listed emails in', function () {
    $this->actingAs(User::factory()->create(['email' => 'someone@example.com']))
        ->get('/admin')->assertForbidden();

    $this->actingAs(admin())->get('/admin')->assertOk();
});

it('opens every admin list page', function (string $path) {
    $this->actingAs(admin())->get('/admin/'.$path)->assertOk();
})->with(['projects', 'posts', 'education', 'experiences', 'skill-groups', 'tools', 'contact-messages']);

it('edits a project and the change appears on the site', function () {
    $project = Project::ordered()->first();
    $this->actingAs(admin());

    Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
        ->fillForm(['title' => 'Renamed project', 'live_url' => 'https://example.com'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($project->fresh())->title->toBe('Renamed project')->live_url->toBe('https://example.com');
    $this->get('/projects/'.$project->slug)->assertSee('Renamed project');
});

it('creates a blog post with a list block and French left empty', function () {
    $this->actingAs(admin());

    Livewire::test(CreatePost::class)
        ->fillForm([
            'slug' => 'new-post',
            'category' => 'backend',
            'published_at' => now()->subDay()->toDateString(),
            'read_time' => 4,
            'title' => ['en' => 'A new post'],
            'excerpt' => ['en' => 'Short summary'],
            'body' => [
                ['type' => 'p', 'text' => ['en' => 'Hello world']],
                ['type' => 'ul', 'text' => ['en' => "First\nSecond"]],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = Post::where('slug', 'new-post')->sole();
    expect($post->body[1]['text']['en'])->toBe(['First', 'Second']);

    // French falls back to English on the French site.
    $this->get('/fr/blog/new-post')->assertOk()->assertSee('A new post');
});

it('marks a message as read when it is opened', function () {
    $message = ContactMessage::create([
        'name' => 'Jane', 'email' => 'jane@example.com', 'subject' => 'job',
        'message' => 'Hello, are you available for a role?', 'locale' => 'en',
    ]);
    $this->actingAs(admin());

    Livewire::test(ViewContactMessage::class, ['record' => $message->getKey()])->assertOk();

    expect($message->fresh()->read_at)->not->toBeNull();
});
