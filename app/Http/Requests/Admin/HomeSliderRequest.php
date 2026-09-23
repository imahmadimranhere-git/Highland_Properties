<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'];

        return [
            'title' => ['nullable', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99'],
            'is_active' => ['boolean'],

            // On create all three crops are required, so a banner is never
            // added half-finished. On edit each one is optional (replace only
            // what changed) and completeness is enforced by the public scope.
            'image' => [$creating ? 'required' : 'nullable', ...$image],
            'image_tablet' => [$creating ? 'required' : 'nullable', ...$image],
            'image_mobile' => [$creating ? 'required' : 'nullable', ...$image],
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => 'laptop image',
            'image_tablet' => 'tablet image',
            'image_mobile' => 'mobile image',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Upload the laptop banner (1920 × 900).',
            'image_tablet.required' => 'Upload the tablet banner (1024 × 800).',
            'image_mobile.required' => 'Upload the mobile banner (800 × 1000).',
        ];
    }
}
