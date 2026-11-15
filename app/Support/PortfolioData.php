<?php

namespace App\Support;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Builds the `App.data` payload the front-end scripts render from.
 *
 * Every page gets the profile plus empty collections by default; a
 * controller passes only the collections its page actually displays.
 */
final class PortfolioData
{
    private const COLLECTIONS = [
        'projects', 'education', 'experience', 'posts', 'skills', 'tools',
    ];

    /**
     * @param  array<string, JsonResource|array>  $collections
     */
    public static function make(array $collections = []): array
    {
        $unknown = array_diff(array_keys($collections), self::COLLECTIONS);

        if ($unknown !== []) {
            throw new \InvalidArgumentException('Unknown portfolio collection(s): '.implode(', ', $unknown));
        }

        $data = ['profile' => self::profile()]
            + array_fill_keys(self::COLLECTIONS, []);

        foreach ($collections as $key => $value) {
            $data[$key] = self::withoutEmptyTranslations(
                $value instanceof JsonResource ? $value->resolve() : $value
            );
        }

        return $data;
    }

    /**
     * Drops empty languages from { en, fr, ar } values so the front end
     * falls back to English instead of showing an empty or "null" text.
     */
    public static function withoutEmptyTranslations(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $value = array_map(self::withoutEmptyTranslations(...), $value);

        $isTranslationMap = $value !== [] && ! array_is_list($value)
            && array_diff(array_keys($value), Locale::SUPPORTED) === [];

        return $isTranslationMap
            ? array_filter($value, fn ($text) => filled($text))
            : $value;
    }

    /**
     * Profile from config/portfolio.php with absolute URLs for its files.
     */
    public static function profile(): array
    {
        $profile = config('portfolio.profile');
        $profile['photo'] = Media::url($profile['photo']);
        $profile['cv'] = Media::url($profile['cv']);

        return $profile;
    }

    /**
     * Profile values for Blade views, in the current language.
     * Mirrors the [data-profile] bindings the front end fills in.
     */
    public static function viewProfile(): array
    {
        $p = self::profile();
        $locale = app()->getLocale();
        $pick = fn (array|string $value) => is_array($value) ? ($value[$locale] ?? $value['en']) : $value;

        return [
            'name' => $pick($p['name']),
            'handle' => $p['handle'],
            'email' => $p['email'],
            'phone' => $p['phone'],
            'location' => $pick($p['location']),
            'photo' => $p['photo'],
            'href' => [
                'email' => 'mailto:'.$p['email'],
                'phone' => 'tel:'.preg_replace('/\s+/', '', $p['phone']),
                'cv' => $p['cv'],
            ] + $p['socials'],
        ];
    }
}
