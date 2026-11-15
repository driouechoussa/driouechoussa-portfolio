<?php

use App\Models\Post;
use App\Models\Project;
use Database\Seeders\PortfolioSeeder;

beforeEach(function () {
    $this->seed(PortfolioSeeder::class);
});

it('serves every page in every language', function (string $prefix, string $lang, string $dir) {
    foreach (['', '/about', '/projects', '/blog', '/contact', '/privacy'] as $path) {
        $this->get($prefix.$path ?: '/')
            ->assertOk()
            ->assertSee('<html lang="'.$lang.'" dir="'.$dir.'"', false);
    }
})->with([
    'english' => ['', 'en', 'ltr'],
    'french' => ['/fr', 'fr', 'ltr'],
    'arabic' => ['/ar', 'ar', 'rtl'],
]);

it('renders page text on the server in the page language', function () {
    $this->get('/fr/about')->assertSee(__('ui.nav.about', [], 'fr'));
    $this->get('/ar/about')->assertSee(__('ui.nav.about', [], 'ar'));
});

it('links each page to its translations with hreflang and a canonical url', function () {
    $project = Project::ordered()->first();

    $this->get('/fr/projects/'.$project->slug)
        ->assertSee('<link rel="canonical" href="'.url('/fr/projects/'.$project->slug).'">', false)
        ->assertSee('hreflang="en" href="'.url('/projects/'.$project->slug).'"', false)
        ->assertSee('hreflang="ar" href="'.url('/ar/projects/'.$project->slug).'"', false)
        ->assertSee('hreflang="x-default"', false);
});

it('keeps links inside the current language', function () {
    $this->get('/ar')->assertSee('data-root="'.url('/ar').'/"', false)
        ->assertSee('href="'.url('/ar/projects').'"', false);
});

it('rejects unsupported language prefixes', function () {
    $this->get('/de/about')->assertNotFound();
});

it('shows localized error pages', function () {
    $this->get('/fr/does-not-exist')
        ->assertNotFound()
        ->assertSee(__('ui.error.404.title', [], 'fr'))
        ->assertSee('<html lang="fr"', false);

    $this->get('/ar/projects/does-not-exist')
        ->assertNotFound()
        ->assertSee(__('ui.error.404.title', [], 'ar'));
});

it('localizes detail page titles', function () {
    $post = Post::published()->first();

    $this->get('/fr/blog/'.$post->slug)->assertSee('<title>'.e($post->title['fr']), false);
});
