<?php

namespace App\Http\Requests;

use App\Models\ContactMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Honeypot: a hidden "website" field that people never fill in.
     */
    public function isSpam(): bool
    {
        return filled($this->input('website'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:150'],
            'subject' => ['required', Rule::in(ContactMessage::SUBJECTS)],
            'budget' => ['nullable', Rule::in(ContactMessage::BUDGETS)],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ];
    }

    /**
     * Messages come from the site's own UI strings so they match the
     * front-end validation in every language.
     */
    public function messages(): array
    {
        return [
            'required' => __('ui.form.errRequired'),
            'email' => __('ui.form.errEmail'),
            'message.min' => __('ui.form.errMessage'),
            'max' => __('ui.form.errTooLong'),
            'in' => __('ui.form.errChoice'),
        ];
    }
}
