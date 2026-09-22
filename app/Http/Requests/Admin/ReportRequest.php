<?php

namespace App\Http\Requests\Admin;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'user_id' => ['required', 'exists:users,id'],
            'project_id' => ['nullable', 'exists:projects,id'],

            // Monthly performance covers a period; every other type is one day.
            'report_date' => [$isPeriod ? 'nullable' : 'required', 'date'],
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
            'attachment.max' => 'The attachment must be 10 MB or smaller.',
            'attachment.mimes' => 'Attach a PDF, image, Excel, CSV or Word file.',
        ];
    }
}
