<?php

namespace App\Filament\Support;

use App\Support\Locale;
use Closure;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Builds one tab per site language for { en, fr, ar } JSON fields.
 *
 *   Translatable::tabs('Content', fn (string $locale) => [
 *       TextInput::make("title.$locale")->label('Title')->required($locale === 'en'),
 *   ])
 */
final class Translatable
{
    private const LABELS = ['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية'];

    /**
     * @param  Closure(string): array  $fields  receives the locale, returns the tab's fields
     */
    public static function tabs(string $label, Closure $fields): Tabs
    {
        return Tabs::make($label)
            ->tabs(array_map(
                fn (string $locale) => Tab::make(self::LABELS[$locale])->schema($fields($locale)),
                Locale::SUPPORTED,
            ))
            ->columnSpanFull();
    }

    /**
     * Only English is required; French and Arabic fall back to English.
     */
    public static function required(string $locale): bool
    {
        return $locale === Locale::DEFAULT;
    }
}
