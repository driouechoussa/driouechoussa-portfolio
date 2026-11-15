<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

/**
 * Site languages. English lives at the root (/projects); other languages
 * are prefixed (/fr/projects, /ar/projects). Prefixed routes share the
 * same names with a "localized." prefix.
 */
final class Locale
{
    public const DEFAULT = 'en';

    public const SUPPORTED = ['en', 'fr', 'ar'];

    /** Languages that live under a URL prefix. */
    public const PREFIXED = ['fr', 'ar'];

    /** Values for og:locale. */
    private const OG = ['en' => 'en_US', 'fr' => 'fr_FR', 'ar' => 'ar_MA'];

    public static function current(): string
    {
        return app()->getLocale();
    }

    public static function dir(?string $locale = null): string
    {
        return ($locale ?? self::current()) === 'ar' ? 'rtl' : 'ltr';
    }

    public static function og(?string $locale = null): string
    {
        return self::OG[$locale ?? self::current()];
    }

    /**
     * URL of a named site route in the given (or current) language.
     */
    public static function route(string $name, mixed $parameters = [], ?string $locale = null): string
    {
        $locale ??= self::current();
        $parameters = is_array($parameters) ? $parameters : [$parameters];

        return $locale === self::DEFAULT
            ? route($name, $parameters)
            : route('localized.'.$name, ['locale' => $locale] + $parameters);
    }

    /**
     * The current page in every language, keyed by locale.
     * Empty when the request didn't match a named site route (e.g. 404).
     *
     * @return array<string, string>
     */
    public static function alternates(?Request $request = null): array
    {
        $route = ($request ?? request())->route();

        // Only pages (GET routes) have language versions; not e.g. POST /contact.
        if (! $route instanceof Route || ! $route->getName() || ! in_array('GET', $route->methods(), true)) {
            return [];
        }

        $name = str_starts_with($route->getName(), 'localized.')
            ? substr($route->getName(), strlen('localized.'))
            : $route->getName();

        // Routes outside the localized site (admin panel, sitemap...) have one URL only.
        if (! RouteFacade::has('localized.'.$name)) {
            return [];
        }

        $parameters = collect($route->parameters())->except('locale')->all();

        $urls = [];
        foreach (self::SUPPORTED as $locale) {
            $urls[$locale] = self::route($name, $parameters, $locale);
        }

        return $urls;
    }

    /**
     * Pick the language from the first URL segment (used for error pages,
     * where no route matched and the locale middleware did not run).
     */
    public static function fromPath(Request $request): string
    {
        $segment = $request->segment(1);

        return in_array($segment, self::PREFIXED, true) ? $segment : self::DEFAULT;
    }
}
