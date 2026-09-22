<?php

namespace App\Http\Requests\Admin;

use App\Enums\LeadSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Manual lead entry and editing a lead's contact details. */
class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string', 'max:3000'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'unit_category_id' => [
                'nullable',
                Rule::exists('unit_categories', 'id')->where('project_id', $this->integer('project_id')),
            ],
            'source' => ['required', Rule::enum(LeadSource::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'next_follow_up_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a phone number using digits, spaces, + or -.',
            'unit_category_id.exists' => 'That category does not belong to the chosen project.',
        ];
    }
}
