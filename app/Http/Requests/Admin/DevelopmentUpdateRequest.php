<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DevelopmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'update_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['boolean'],

            'photos' => ['nullable', 'array', 'max:12'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_published' => $this->boolean('is_published')]);
    }

    public function messages(): array
    {
        return [
            'update_date.before_or_equal' => 'A site update cannot be dated in the future.',
        ];
    }
}
