<?php

/*
|--------------------------------------------------------------------------
| Portfolio profile
|--------------------------------------------------------------------------
|
| Personal details shown across the site (header, footer, contact page,
| author box). Localized values use the { en, fr, ar } shape the front end
| expects. Asset paths are relative to the public directory.
|
*/

return [

    'profile' => [
        'name' => [
            'en' => 'Oussama Driouech',
            'fr' => 'Oussama Driouech',
            'ar' => 'أسامة الدريوش',
        ],
        'initials' => 'OD',
        'handle' => '@driouechoussa',
        'email' => env('PORTFOLIO_EMAIL', 'hello@driouechoussa.me'),
        'phone' => env('PORTFOLIO_PHONE', '+212 600 000 000'),
        'location' => [
            'en' => 'Rabat, Morocco',
            'fr' => 'Rabat, Maroc',
            'ar' => 'الرباط، المغرب',
        ],
        'cv' => 'cv/oussama-driouech-cv.pdf',
        'photo' => 'images/avatar.webp',
        'socials' => [
            'linkedin' => 'https://www.linkedin.com/in/driouechoussa',
            'instagram' => 'https://www.instagram.com/driouechoussa',
            'x' => 'https://x.com/driouechoussa',
            'github' => 'https://github.com/driouechoussa',
        ],
        'job_title' => 'Full-Stack Web Developer',
    ],

    /*
    | Numbers shown in the hero and on the About page ("7+ years", …).
    */
    'stats' => [
        'years' => 7,
        'projects' => 50,
        'clients' => 30,
        'tech' => 12,
    ],

    /*
    | Where contact-form messages are emailed when no email has been saved in
    | the admin panel (Profile → Contact details). Defaults to the profile email.
    | verify_email_dns: the admin email must have a domain that accepts mail.
    */
    'contact' => [
        'recipient' => env('CONTACT_RECIPIENT'),
        'verify_email_dns' => env('CONTACT_VERIFY_EMAIL_DNS', true),
    ],

    /*
    | Admin panel (/admin) access: comma-separated list of allowed emails.
    */
    'admin_emails' => array_filter(array_map('trim', explode(',', (string) env('ADMIN_EMAILS', '')))),

    /*
    | Privacy-friendly analytics (Plausible). Leave PLAUSIBLE_DOMAIN empty to
    | disable. For a self-hosted instance, change PLAUSIBLE_HOST.
    */
    'analytics' => [
        'plausible_domain' => env('PLAUSIBLE_DOMAIN'),
        'plausible_host' => env('PLAUSIBLE_DOMAIN') ? env('PLAUSIBLE_HOST', 'https://plausible.io') : null,
    ],

];
