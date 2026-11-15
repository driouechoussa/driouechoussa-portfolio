<?php

use App\Models\Post;
use App\Models\Project;
use Database\Seeders\PortfolioSeeder;

beforeEach(function () {
    $this->seed(PortfolioSeeder::class);
});

it('renders the static pages', function (string $route) {
    $this->get(route($route))
        ->assertOk()
        ->assertSee('window.App.data', false);
})->with(['home', 'about', 'contact', 'projects.index', 'posts.index']);

it('shows a project by slug', function () {
    $project = Project::ordered()->first();

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('data-id="'.$project->slug.'"', false)
        ->assertSee($project->title);
});

it('shows a published post by slug', function () {
    $post = Post::published()->first();

    $this->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('data-id="'.$post->slug.'"', false);
});

it('returns 404 for unknown slugs', function () {
    $this->get('/projects/does-not-exist')->assertNotFound();
    $this->get('/blog/does-not-exist')->assertNotFound();
});

it('hides posts scheduled for the future', function () {
    $post = Post::published()->first();
    $post->update(['published_at' => now()->addWeek()]);

    $this->get(route('posts.show', $post))->assertNotFound();
    $slugs = array_column($this->get(route('posts.index'))->viewData('appData')['posts'], 'id');
    expect($slugs)->not->toContain($post->slug);
});

it('passes the data shape the front end expects', function () {
    $data = $this->get(route('about'))->viewData('appData');

    expect($data)->toHaveKeys(['profile', 'projects', 'education', 'experience', 'posts', 'skills', 'tools'])
        ->and($data['skills'][0])->toHaveKeys(['group', 'icon', 'items'])
        ->and($data['skills'][0]['items'][0])->toHaveCount(2)
        ->and($data['experience'][0])->toHaveKeys(['start', 'end', 'role', 'company', 'location', 'desc']);
});
