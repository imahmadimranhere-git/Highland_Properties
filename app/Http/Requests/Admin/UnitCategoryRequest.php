<?php

namespace App\Http\Requests\Admin;

use App\Enums\InstallmentFrequency;
use App\Enums\UnitAvailability;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The category and its payment plan are one form, because a category
     * without a plan is not something a buyer can be quoted from.
     */
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

            'plan.booking_amount' => ['required', 'numeric', 'min:0'],
            'plan.down_payment' => ['required', 'numeric', 'min:0'],
            'plan.installment_count' => ['required', 'integer', 'min:0', 'max:500'],
            'plan.installment_frequency' => ['required', Rule::enum(InstallmentFrequency::class)],
            'plan.installment_amount' => ['required', 'numeric', 'min:0'],
            'plan.possession_charges' => ['required', 'numeric', 'min:0'],
            'plan.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'plan.booking_amount' => 'booking amount',
            'plan.down_payment' => 'down payment',
            'plan.installment_count' => 'number of installments',
            'plan.installment_frequency' => 'installment frequency',
            'plan.installment_amount' => 'installment amount',
            'plan.possession_charges' => 'possession charges',
        ];
    }
}
