<?php

namespace App\Providers;

use App\Support\PortfolioData;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Refuse migrate:fresh, db:wipe, etc. on the live database.
        DB::prohibitDestructiveCommands($this->app->isProduction());

        // Behind a load balancer / reverse proxy (TRUSTED_PROXIES="*" or a list of IPs),
        // trust its X-Forwarded-* headers so HTTPS and client IPs are detected correctly.
        if ($proxies = config('app.trusted_proxies')) {
            TrustProxies::at($proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        // Profile details (name, email, socials…) in the current language for every site view.
        View::composer(['layouts.*', 'pages.*', 'sections.*', 'errors.*'], function ($view) {
            $view->with('profile', PortfolioData::viewProfile());
        });

        // Contact form: a few messages per minute and a daily cap per visitor.
        RateLimiter::for('contact', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perDay(20)->by($request->ip()),
            ];
        });
    }
}
