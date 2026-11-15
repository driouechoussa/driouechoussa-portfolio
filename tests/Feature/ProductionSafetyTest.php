<?php

use App\Models\Project;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Artisan;

afterEach(function () {
    // TrustProxies::at() is static; reset it so other tests are unaffected.
    TrustProxies::flushState();
});

it('seeds an empty database in production', function () {
    app()->detectEnvironment(fn () => 'production');

    // As in deploy.sh: production requires --force.
    $this->artisan('db:seed', ['--force' => true])->assertSuccessful();

    expect(Project::count())->toBeGreaterThan(0);
});

it('never overwrites admin edits when seeding again in production', function () {
    $this->seed(PortfolioSeeder::class);
    $project = Project::ordered()->first();
    $project->update(['title' => 'Edited in the admin panel']);

    app()->detectEnvironment(fn () => 'production');
    $this->artisan('db:seed', ['--force' => true])
        ->expectsOutputToContain('skipped so admin edits are not overwritten')
        ->assertSuccessful();

    expect($project->fresh()->title)->toBe('Edited in the admin panel');
});

it('reads trusted proxies from config so they survive config caching', function () {
    config(['app.trusted_proxies' => '*']);
    app()->getProvider(App\Providers\AppServiceProvider::class)->boot();

    $this->get('/up', ['X-Forwarded-Proto' => 'https', 'X-Forwarded-For' => '203.0.113.9'])->assertOk();

    expect(request()->isSecure())->toBeTrue()
        ->and(request()->ip())->toBe('203.0.113.9');
});

it('refuses destructive database commands in production', function () {
    app()->detectEnvironment(fn () => 'production');
    app()->getProvider(App\Providers\AppServiceProvider::class)->boot();

    expect(Artisan::call('db:wipe', ['--force' => true]))->toBe(1);
    expect(Project::query()->getConnection()->getSchemaBuilder()->hasTable('projects'))->toBeTrue();
});
