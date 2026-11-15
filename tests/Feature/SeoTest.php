<?php

use App\Models\Post;
use App\Models\Project;
use Database\Seeders\PortfolioSeeder;

beforeEach(function () {
    $this->seed(PortfolioSeeder::class);
});

it('lists every page, project and article in every language in the sitemap', function () {
    $project = Project::ordered()->first();
    $post = Post::published()->first();

    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    foreach (['', '/fr', '/ar'] as $prefix) {
        $response->assertSee('<loc>'.url($prefix.'/projects/'.$project->slug).'</loc>', false)
            ->assertSee('<loc>'.url($prefix.'/blog/'.$post->slug).'</loc>', false)
            ->assertSee('<loc>'.url($prefix.'/privacy').'</loc>', false);
    }

    // 6 pages + 8 projects + 6 posts, each in 3 languages.
    expect(substr_count($response->getContent(), '<url>'))->toBe((6 + Project::count() + Post::published()->count()) * 3);
});

it('leaves future posts out of the sitemap', function () {
    $post = Post::published()->first();
    $post->update(['published_at' => now()->addMonth()]);

    $this->get('/sitemap.xml')->assertDontSee('/blog/'.$post->slug.'<', false);
});

it('blocks crawlers outside production and points to the sitemap in production', function () {
    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');

    app()->detectEnvironment(fn () => 'production');
    // Production redirects plain HTTP to HTTPS, so request over HTTPS.
    $this->get(str_replace('http://', 'https://', url('/robots.txt')))
        ->assertOk()
        ->assertSee('Sitemap: '.str_replace('http://', 'https://', url('/sitemap.xml')))
        ->assertDontSee("Disallow: /\n", false);
});

it('adds social and structured data tags', function () {
    $post = Post::published()->first();

    $this->get('/')
        ->assertSee('<meta property="og:image" content="'.asset('images/og-image.png').'">', false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
        ->assertSee('"@type":"Person"', false);

    $this->get('/blog/'.$post->slug)
        ->assertSee('<meta property="og:type" content="article">', false)
        ->assertSee('"@type":"BlogPosting"', false)
        ->assertSee(e($post->body[0]['text']['en'] ?? ''), false); // article text is in the server HTML
});

it('sends security headers with a script nonce', function () {
    $response = $this->get('/');

    $csp = $response->headers->get('Content-Security-Policy');
    preg_match("/'nonce-([^']+)'/", $csp, $m);

    expect($csp)->toContain("default-src 'self'")->toContain("object-src 'none'")
        ->and($m[1] ?? null)->not->toBeNull();

    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertSee('nonce="'.$m[1].'"', false);
});
