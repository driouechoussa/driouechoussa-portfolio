<?php

use App\Support\Locale;

if (! function_exists('localized_route')) {
    /**
     * URL of a named site route in the current (or given) language.
     *
     *   localized_route('projects.show', $project)       // /fr/projects/souk-digital on a French page
     *   localized_route('contact', locale: 'ar')         // /ar/contact
     */
    function localized_route(string $name, mixed $parameters = [], ?string $locale = null): string
    {
        return Locale::route($name, $parameters, $locale);
    }
}
