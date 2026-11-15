<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Support\Locale;
use App\Support\PortfolioData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact', [
            'appData' => PortfolioData::make(),
        ]);
    }

    public function store(StoreContactMessageRequest $request): JsonResponse|RedirectResponse
    {
        // Bots that fill the honeypot get the normal success response, but nothing is saved or sent.
        if (! $request->isSpam()) {
            $message = ContactMessage::create($request->validated() + [
                'locale' => Locale::current(),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            ]);

            $this->notifyOwner($message);
        }

        $status = __('ui.form.success');

        return $request->expectsJson()
            ? response()->json(['message' => $status])
            : redirect()->to(localized_route('contact'))->with('status', $status);
    }

    /**
     * The message is already stored, so a mail failure must not lose it or
     * show the visitor an error; it is logged and visible in the admin panel.
     */
    private function notifyOwner(ContactMessage $message): void
    {
        $recipient = config('portfolio.contact.recipient') ?: config('portfolio.profile.email');

        try {
            Mail::to($recipient)->send(new NewContactMessage($message));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
