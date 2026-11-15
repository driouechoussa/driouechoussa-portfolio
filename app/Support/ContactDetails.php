<?php

namespace App\Support;

use App\Models\Setting;
use App\Rules\EmailDomainAcceptsMail;

/**
 * Email, phone, location and social links shown on the site.
 *
 * Values saved in the admin panel (Profile → Contact details) override the
 * defaults in config/portfolio.php. The email is also where contact-form
 * messages are delivered.
 */
final class ContactDetails
{
    public const SETTING = 'contact_details';

    /**
     * Social networks the site shows, with the domains a link must point to.
     * The same keys are used by the front end (data-profile-href="linkedin", ...).
     */
    public const SOCIALS = [
        'linkedin' => ['label' => 'LinkedIn', 'icon' => 'fa-brands fa-linkedin-in', 'domains' => ['linkedin.com']],
        'instagram' => ['label' => 'Instagram', 'icon' => 'fa-brands fa-instagram', 'domains' => ['instagram.com']],
        'x' => ['label' => 'X (Twitter)', 'icon' => 'fa-brands fa-x-twitter', 'domains' => ['x.com', 'twitter.com']],
        'github' => ['label' => 'GitHub', 'icon' => 'fa-brands fa-github', 'domains' => ['github.com']],
    ];

    /**
     * @return array{email: string, phone: string, location: array<string, string>, socials: array<string, string>}
     */
    public static function all(): array
    {
        $profile = config('portfolio.profile');
        $saved = Setting::get(self::SETTING) ?? [];

        $details = [
            'email' => $saved['email'] ?? $profile['email'],
            'phone' => $saved['phone'] ?? $profile['phone'],
            'location' => $saved['location'] ?? $profile['location'],
            'socials' => array_key_exists('socials', $saved) ? $saved['socials'] : $profile['socials'],
        ];

        // Empty languages fall back to English; empty social links are hidden.
        $details['location'] = array_filter((array) $details['location'], 'filled');
        $details['socials'] = array_filter(
            array_intersect_key((array) $details['socials'], self::SOCIALS),
            'filled',
        );

        return $details;
    }

    public static function save(array $details): void
    {
        Setting::set(self::SETTING, [
            'email' => trim($details['email']),
            'phone' => trim((string) ($details['phone'] ?? '')),
            'location' => array_map(fn ($v) => trim((string) $v), $details['location'] ?? []),
            'socials' => array_map(fn ($v) => trim((string) $v), $details['socials'] ?? []),
        ]);
    }

    /**
     * Where contact-form messages go: the email saved in the admin panel,
     * otherwise CONTACT_RECIPIENT, otherwise the default profile email.
     */
    public static function recipient(): string
    {
        $saved = Setting::get(self::SETTING)['email'] ?? null;

        return $saved ?: (config('portfolio.contact.recipient') ?: config('portfolio.profile.email'));
    }

    /**
     * Rules for an email that must be able to receive mail: valid syntax and,
     * unless disabled (tests, offline), a domain with mail (MX/A) DNS records.
     *
     * @return array<int, mixed>
     */
    public static function emailRules(): array
    {
        $rules = ['required', 'max:254', 'email:rfc,strict'];

        if (config('portfolio.contact.verify_email_dns', true)) {
            $rules[] = new EmailDomainAcceptsMail;
        }

        return $rules;
    }

    /**
     * Whether a URL is https and on one of the network's domains (or a subdomain).
     */
    public static function isValidSocialUrl(string $network, string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if (parse_url($url, PHP_URL_SCHEME) !== 'https' || $host === '') {
            return false;
        }

        foreach (self::SOCIALS[$network]['domains'] ?? [] as $domain) {
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                return true;
            }
        }

        return false;
    }
}
