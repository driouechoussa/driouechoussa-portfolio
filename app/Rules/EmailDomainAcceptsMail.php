<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

/**
 * The email's domain must exist and be able to receive mail: it has MX
 * records, or (RFC 5321 fallback) resolves to an address.
 *
 * Laravel's "email:dns" relies on raw DNS queries (MX/A) only, which some
 * networks block; this rule also tries the system resolver before failing.
 */
class EmailDomainAcceptsMail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = strtolower(rtrim(Str::afterLast((string) $value, '@'), '.'));

        if ($domain === '' || ! str_contains($domain, '.') || ! $this->acceptsMail($domain)) {
            $fail('The domain of this email address cannot receive mail.');
        }
    }

    private function acceptsMail(string $domain): bool
    {
        $ascii = function_exists('idn_to_ascii') ? (idn_to_ascii($domain) ?: $domain) : $domain;

        if (@checkdnsrr($ascii.'.', 'MX') || @checkdnsrr($ascii.'.', 'A') || @checkdnsrr($ascii.'.', 'AAAA')) {
            return true;
        }

        // System resolver (hosts file, OS DNS cache): returns the name unchanged on failure.
        return gethostbyname($ascii.'.') !== $ascii.'.';
    }
}
