<?php

namespace App\Models\Concerns;

/**
 * Reads { en, fr, ar } JSON attributes in the current language,
 * falling back to English.
 */
trait HasLocalizedFields
{
    public function localized(string $attribute, ?string $locale = null): mixed
    {
        return self::pickLocale($this->getAttribute($attribute), $locale);
    }

    public static function pickLocale(mixed $value, ?string $locale = null): mixed
    {
        if (! is_array($value) || array_is_list($value)) {
            return $value;
        }

        $locale ??= app()->getLocale();

        return filled($value[$locale] ?? null) ? $value[$locale] : ($value['en'] ?? null);
    }
}
