<?php

namespace App\Http\Requests\Admin;

use App\Enums\TestimonialStatus;
use App\Http\Requests\Concerns\DefaultsSortOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestimonialRequest extends FormRequest
{
    use DefaultsSortOrder;

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'youtube_url.regex' => 'Paste a YouTube link, for example https://youtu.be/xxxxxxxxxxx',
            'message.required_without' => 'Write the testimonial, or add a video link instead.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->defaultSortOrder());
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:120'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            // A testimonial needs words or a video — either alone is fine.
            'message' => ['nullable', 'required_without:youtube_url', 'string', 'max:2000'],

            // Optional video testimonial. Any YouTube link shape is accepted;
            // the video id is parsed out when the page is rendered.
            'youtube_url' => ['nullable', 'url', 'max:255', 'regex:~(youtube\\.com|youtu\\.be)~i'],
            'status' => ['required', Rule::enum(TestimonialStatus::class)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
