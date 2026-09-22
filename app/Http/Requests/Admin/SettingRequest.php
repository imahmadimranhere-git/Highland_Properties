<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * One request class for every settings tab. Only the fields belonging to the
 * submitted group are validated and saved, so saving "Social" can never wipe
 * the phone number on the "Contact" tab.
 */
class SettingRequest extends FormRequest
{
    public const GROUPS = [
        'general' => [
            'site_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'logo' => ['nullable', 'file', 'mimes:svg,png,webp', 'max:512'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,svg', 'max:128'],
        ],
        'contact' => [
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s]+$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'map_embed_url' => ['nullable', 'url', 'max:500'],
        ],
        'social' => [
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
        ],
        'seo' => [
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ],
        'about' => [
            'about_heading' => ['nullable', 'string', 'max:200'],
            'about_content' => ['nullable', 'string', 'max:20000'],
        ],
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return self::GROUPS[$this->route('group')] ?? [];
    }

    public function messages(): array
    {
        return [
            'whatsapp.regex' => 'Use digits only with the country code, for example 923001234567.',
            'logo.mimes' => 'Upload the logo as SVG, PNG or WebP.',
        ];
    }
}
