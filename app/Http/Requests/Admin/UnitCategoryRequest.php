<?php

namespace App\Http\Requests\Admin;

use App\Enums\UnitAvailability;
use App\Http\Requests\Concerns\DefaultsSortOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitCategoryRequest extends FormRequest
{
    use DefaultsSortOrder;

    public function authorize(): bool
    {
        return true;
    }

    /** Unit details only — payment plans are no longer edited here. */
    protected function prepareForValidation(): void
    {
        $this->merge($this->defaultSortOrder());
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'unit_type' => ['required', 'string', 'max:120'],
            'size_value' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'size_unit' => ['required', 'string', 'max:20'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:60'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:60'],
            'total_price' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'availability' => ['required', Rule::enum(UnitAvailability::class)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}
