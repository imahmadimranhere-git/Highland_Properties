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
            // No tight limit: a logo is uploaded once and is not what slows
            // a site down. 4 MB simply stops an accidental camera photo.
            'logo' => ['nullable', 'file', 'mimes:svg,png,webp,jpg,jpeg', 'max:4096'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,svg,webp', 'max:2048'],
        ],
        'contact' => [
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s]+$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'map_embed_url' => ['nullable', 'string', 'max:2000'],
            // The first line of a WhatsApp chat started from a project page.
            'whatsapp_message' => ['nullable', 'string', 'max:300'],
        ],
        'social' => [
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
        ],
        'video' => [
            'video_heading' => ['nullable', 'string', 'max:120'],
            'video_text' => ['nullable', 'string', 'max:300'],
            // Any YouTube URL shape is accepted; the id is parsed out later.
            'youtube_video_url' => ['nullable', 'url', 'max:255', 'regex:~(youtube\\.com|youtu\\.be)~i'],
            'youtube_channel_url' => ['nullable', 'url', 'max:255', 'regex:~youtube\\.com~i'],
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
            'youtube_video_url.regex' => 'Paste a YouTube link, for example https://youtu.be/xxxxxxxxxxx',
            'youtube_channel_url.regex' => 'Paste a YouTube channel link, for example https://youtube.com/@yourchannel',
            'whatsapp.regex' => 'Use digits only with the country code, for example 923001234567.',
            'logo.mimes' => 'Upload the logo as SVG, PNG, WebP or JPG.',
        ];
    }
}
