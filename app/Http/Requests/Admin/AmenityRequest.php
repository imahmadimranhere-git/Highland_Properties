<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmenityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('amenities', 'name')->ignore($this->route('amenity')?->id),
            ],
            // Must match a key in the <x-ui.icon> component.
            'icon' => ['nullable', 'string', 'max:60', 'alpha_dash'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
