<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\DefaultsSortOrder;
use Illuminate\Foundation\Http\FormRequest;

class TickerRequest extends FormRequest
{
    use DefaultsSortOrder;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:200'],
            // Its own link, separate from every other announcement.
            'link' => ['nullable', 'string', 'max:500', 'starts_with:http://,https://,/'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->defaultSortOrder() + [
            'is_active' => $this->boolean('is_active'),
            'link' => trim((string) $this->input('link')) ?: null,
        ]);
    }

    public function messages(): array
    {
        return [
            'link.starts_with' => 'The link must begin with http://, https:// or / for a page on this site.',
        ];
    }
}
