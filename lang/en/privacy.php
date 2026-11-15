<?php

return [
    'eyebrow' => 'Legal',
    'title' => 'Privacy <span class="accent">policy</span>',
    'lead' => 'What happens to the information you share on this website.',
    'updated' => 'Last updated: :date',
    'sections' => [
        [
            'heading' => 'Who is responsible',
            'body' => ['This website is run by :name (:email). If you have any question about your data, write to that address.'],
        ],
        [
            'heading' => 'What is collected through the contact form',
            'body' => ['When you send a message, the following is stored so I can read and answer it:'],
            'list' => [
                'your name and email address',
                'the subject and message you choose to send',
                'the language of the page you used',
                'your IP address and browser type, only to block spam and abuse',
            ],
        ],
        [
            'heading' => 'Why it is used',
            'body' => ['Your message is used only to reply to you and to discuss the request you sent. It is never sold, never used for advertising and never added to a mailing list without your permission.'],
        ],
        [
            'heading' => 'How long it is kept',
            'body' => ['Messages are kept for as long as needed to handle your request, and deleted at the latest 24 months after the last exchange. You can ask for earlier deletion at any time.'],
        ],
        [
            'heading' => 'Who can see it',
            'body' => ['Only me. Messages are stored on the server that hosts this website and forwarded to my mailbox by an email provider. These providers process the data on my behalf and only to deliver this service.'],
        ],
        [
            'heading' => 'Cookies and local storage',
            'body' => ['This website uses only what it needs to work:'],
            'list' => [
                'a session cookie and a security token (XSRF-TOKEN) that protect the contact form against forged requests',
                'your theme (dark or light) and language choice, saved in your own browser (local storage)',
            ],
            'after' => 'There are no advertising or tracking cookies. If visit statistics are enabled, they use a privacy-friendly tool (Plausible) that does not use cookies and does not collect personal data.',
        ],
        [
            'heading' => 'Your rights',
            'body' => ['You can ask to see, correct or delete the information you sent, or object to its use, by writing to :email. These rights are guaranteed by Moroccan law 09-08 and, for visitors in the European Union, by the GDPR.'],
        ],
    ],
];
