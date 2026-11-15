<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolves stored media paths to public URLs.
 *  - Absolute URLs are returned unchanged.
 *  - Files shipped in /public (e.g. "images/avatar.png") use asset().
 *  - Anything else is an upload on the "public" disk (storage/app/public).
 */
final class Media
{
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (preg_match('#^(https?:)?//#', $path)) {
            return $path;
        }

        if (is_file(public_path($path))) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}
