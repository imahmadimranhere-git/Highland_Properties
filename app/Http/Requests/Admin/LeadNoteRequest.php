<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LeadNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['nullable', 'required_without:next_follow_up_at', 'string', 'max:2000'],
            'next_follow_up_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required_without' => 'Write a note or set a follow-up date.',
            'next_follow_up_at.after_or_equal' => 'A follow-up cannot be scheduled in the past.',
        ];
    }
}
