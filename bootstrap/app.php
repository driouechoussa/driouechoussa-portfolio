<?php

use App\Http\Middleware\ForceHttps;
use App\Support\Locale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(ForceHttps::class);

        // Trusted proxies (TRUSTED_PROXIES) are applied in AppServiceProvider from
        // config, so they still work when the config is cached in production.

        $middleware->web(append: [
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Error pages render in the language of the URL (/fr/…, /ar/…), even when
        // the error happened before the locale middleware ran (unknown URL, missing model).
        $exceptions->render(function (Throwable $e, Request $request) {
            app()->setLocale(Locale::fromPath($request));

            return null; // continue with the default rendering
        });
    })->create();
