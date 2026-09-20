<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'exists:cities,id'],
            'name' => [
                'required', 'string', 'max:150',
                // The same society name is allowed in a different city.
                Rule::unique('locations', 'name')
                    ->where('city_id', $this->integer('city_id'))
                    ->ignore($this->route('location')?->id),
            ],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
