<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($this->route('post')?->id)],
            'excerpt' => ['nullable', 'string', 'max:320'],
            // Written in Markdown and rendered with Laravel's built-in Str::markdown().
            'content' => ['nullable', 'string', 'max:60000'],
            /*
             * External link. Either both fields are filled or neither is, so a
             * URL can never be saved with nothing to click on — and text can
             * never be saved that leads nowhere.
             *
             * starts_with is the security rule that matters: without it a
             * "javascript:..." address would be accepted and would run when a
             * reader clicked the link.
             */
            'external_link_text' => ['nullable', 'string', 'max:160', 'required_with:external_link_url'],
            'external_link_url' => [
                'nullable', 'url', 'max:500',
                'required_with:external_link_text',
                'starts_with:http://,https://',
            ],

            'published_at' => ['nullable', 'date'],
            'is_published' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            // Trailing spaces from a paste would fail url validation.
            'external_link_url' => trim((string) $this->input('external_link_url')) ?: null,
            'external_link_text' => trim((string) $this->input('external_link_text')) ?: null,
        ]);
    }

    public function attributes(): array
    {
        return [
            'external_link_text' => 'link text',
            'external_link_url' => 'link address',
        ];
    }

    public function messages(): array
    {
        return [
            'external_link_text.required_with' => 'Write the words readers should click on.',
            'external_link_url.required_with' => 'Add the address the link should open.',
            'external_link_url.starts_with' => 'The address must begin with http:// or https://',
        ];
    }
}
