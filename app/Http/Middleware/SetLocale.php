<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the app language from the optional {locale} URL prefix.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $locale = $route->parameter('locale', Locale::DEFAULT);

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Controllers don't receive {locale}; this keeps their signatures
        // (e.g. show(Project $project)) identical for every language.
        $route->forgetParameter('locale');

        return $next($request);
    }
}
