<?php

namespace App\Http\Requests\Consultant;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Same fields as the admin report form, minus the author: a consultant's
 * report always belongs to the consultant, set in the controller, never
 * taken from the request.
 */
class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPeriod = $this->input('type') === ReportType::MonthlyPerformance->value;

        return [
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', Rule::enum(ReportType::class)],
            'project_id' => ['nullable', 'exists:projects,id'],
            'report_date' => [$isPeriod ? 'nullable' : 'required', 'date', 'before_or_equal:today'],
            'period_start' => [$isPeriod ? 'required' : 'nullable', 'date'],
            'period_end' => [$isPeriod ? 'required' : 'nullable', 'date', 'after_or_equal:period_start'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(ReportStatus::class)],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,xlsx,xls,csv,docx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'report_date.before_or_equal' => 'A report cannot be dated in the future.',
            'attachment.max' => 'The attachment must be 10 MB or smaller.',
        ];
    }
}
