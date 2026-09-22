<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['nullable', 'required_without:phone', 'email', 'max:150'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_without' => 'Give us an email or a phone number so we can reply.',
            'phone.required_without' => 'Give us a phone number or an email so we can reply.',
            'phone.regex' => 'Enter a phone number using digits, spaces, + or -.',
        ];
    }
}
