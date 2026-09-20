<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeveloperRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route is already behind role:super_admin middleware.
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('developer')?->id;

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash', Rule::unique('developers', 'slug')->ignore($id)],
            'background' => ['nullable', 'string', 'max:5000'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:200'],
            'completed_projects' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],

            // Logos are SVG or PNG only: a photograph-sized JPEG has no place
            // in a logo slot, and both formats stay crisp at any size.
            'logo' => ['nullable', 'file', 'mimes:svg,png,webp', 'max:512'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'is_active' => 'status',
            'logo' => 'logo file',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.max' => 'The logo must be 512 KB or smaller.',
            'logo.mimes' => 'Upload the logo as SVG, PNG or WebP.',
        ];
    }
}
