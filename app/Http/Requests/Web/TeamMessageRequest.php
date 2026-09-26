<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

/** A message sent to one team member from the public Our Team page. */
class TeamMessageRequest extends FormRequest
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
            'message' => ['required', 'string', 'min:5', 'max:2000'],
            // Honeypot: hidden from people, filled in by most spam bots.
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'We need a phone number to call you back.',
            'phone.regex' => 'Enter a phone number using digits, spaces, + or -.',
            'website.prohibited' => 'Your message could not be sent.',
        ];
    }
}
