<?php

namespace App\Http\Requests\Admin;

use App\Enums\PlotType;
use App\Enums\UnitAvailability;
use App\Http\Requests\Concerns\DefaultsSortOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlotCategoryRequest extends FormRequest
{
    use DefaultsSortOrder;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->defaultSortOrder());
    }

    public function rules(): array
    {
        return [
            'block' => ['nullable', 'string', 'max:60'],
            'plot_type' => ['required', Rule::enum(PlotType::class)],

            // Written exactly as the society advertises it: "5 Marla",
            // "1 Kanal", "2 Kanal 10 Marla", "1 Acre".
            'size_label' => ['required', 'string', 'max:60'],
            'area_sqft' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'dimensions' => ['nullable', 'string', 'max:60'],

            'price_per_marla' => ['nullable', 'numeric', 'min:0'],
            'total_price' => ['required', 'numeric', 'min:0'],

            'availability' => ['required', Rule::enum(UnitAvailability::class)],
            'total_plots' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'available_plots' => ['nullable', 'integer', 'min:0', 'max:100000', 'lte:total_plots'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'size_label.required' => 'Write the plot size, for example "10 Marla" or "1 Kanal".',
            'available_plots.lte' => 'Available plots cannot be more than the total number of plots.',
        ];
    }
}
