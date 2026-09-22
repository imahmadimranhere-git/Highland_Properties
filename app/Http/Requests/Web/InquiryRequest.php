<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Project inquiry form. Saved as a Lead and routed to the project's consultant. */
class InquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string', 'max:2000'],
            'project_id' => ['required', Rule::exists('projects', 'id')->where('is_published', true)->whereNull('deleted_at')],
            'unit_category_id' => [
                'nullable',
                Rule::exists('unit_categories', 'id')->where('project_id', $this->integer('project_id')),
            ],
            // Honeypot: hidden from people, filled in by most spam bots.
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your name.',
            'phone.required' => 'We need a phone number to call you back.',
            'phone.regex' => 'Enter a phone number using digits, spaces, + or -.',
            'website.prohibited' => 'Your message could not be sent.',
        ];
    }
}
