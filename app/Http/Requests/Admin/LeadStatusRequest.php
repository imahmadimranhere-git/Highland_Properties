<?php

namespace App\Http\Requests\Admin;

use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Shared by the admin panel and, in step 5, the consultant portal. */
class LeadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(LeadStatus::class)],
            'note' => ['nullable', 'string', 'max:2000'],
            // A won deal without a value would break the sales report.
            'deal_value' => ['nullable', 'required_if:status,closed_won', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return ['deal_value.required_if' => 'Enter the deal value when marking a lead as won.'];
    }
}
