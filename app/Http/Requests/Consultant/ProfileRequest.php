<?php

namespace App\Http\Requests\Consultant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Consultants can change how they appear, not who they are:
 * email (the login), role and active status stay with the admin.
 */
class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'designation' => ['nullable', 'string', 'max:120'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
