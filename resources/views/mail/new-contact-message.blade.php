<x-mail::message>
# New message from the website

**Name:** {{ $contactMessage->name }}<br>
**Email:** {{ $contactMessage->email }}<br>
**Subject:** {{ $contactMessage->subject }}<br>
**Budget:** {{ $contactMessage->budget ?? '—' }}<br>
**Language:** {{ $contactMessage->locale }}

<x-mail::panel>
{{ $contactMessage->message }}
</x-mail::panel>

Reply to this email to answer {{ $contactMessage->name }} directly.
</x-mail::message>
