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
            'published_at' => ['nullable', 'date'],
            'is_published' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_published' => $this->boolean('is_published')]);
    }
}
