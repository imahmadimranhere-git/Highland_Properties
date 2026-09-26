<?php

namespace App\Http\Requests\Admin;

use App\Enums\OwnershipFlag;
use App\Enums\ProjectStatus;
use App\Http\Requests\Concerns\DefaultsSortOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    use DefaultsSortOrder;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('project')?->id;

        return [
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($id)],
            'developer_id' => ['required', 'exists:developers,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'project_type_id' => ['nullable', 'exists:project_types,id'],
            'assigned_consultant_id' => ['nullable', 'exists:users,id'],

            'ownership_flag' => ['required', Rule::enum(OwnershipFlag::class)],
            'status' => ['required', Rule::enum(ProjectStatus::class)],

            'short_description' => ['nullable', 'string', 'max:320'],
            'description' => ['nullable', 'string', 'max:20000'],

            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'nearby_landmarks' => ['nullable', 'array', 'max:12'],
            'nearby_landmarks.*' => ['string', 'max:160'],

            'total_area' => ['nullable', 'string', 'max:60'],
            'total_floors' => ['nullable', 'integer', 'min:0', 'max:300'],
            'total_units' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'completion_target' => ['nullable', 'date'],
            'approvals' => ['nullable', 'string', 'max:255'],
            'starting_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999999'],

            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],

            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],

            // 6MB per photo: a modern phone photo fits, a raw camera file does not.
            // Everything is downscaled and converted to WebP on upload anyway.
            // Three crops of the cover: laptop, tablet, phone.
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'cover_tablet' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'cover_mobile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'location_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'gallery' => ['nullable', 'array', 'max:24'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'floor_plans' => ['nullable', 'array', 'max:12'],
            'floor_plans.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:8192'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            ...$this->defaultSortOrder(),
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
            // The landmarks textarea is one entry per line.
            'nearby_landmarks' => collect(preg_split('/\r\n|\r|\n/', (string) $this->input('landmarks_text')))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all(),
        ]);
    }

    /**
     * A draft may be saved with an incomplete cover, but a project cannot go
     * live until all three crops exist — otherwise phones would be served the
     * 1920px laptop image.
     */
    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator) {
                if (! $this->boolean('is_published')) {
                    return;
                }

                $project = $this->route('project');

                $crops = [
                    'cover' => $project?->cover_media_id,
                    'cover_tablet' => $project?->cover_media_id_tablet,
                    'cover_mobile' => $project?->cover_media_id_mobile,
                ];

                foreach ($crops as $field => $existing) {
                    if (! $existing && ! $this->hasFile($field)) {
                        $validator->errors()->add($field, 'Upload the ' . $this->attributes()[$field] . ' before putting the project live.');
                    }
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'cover' => 'laptop cover image',
            'cover_tablet' => 'tablet cover image',
            'cover_mobile' => 'mobile cover image',
        ];
    }

    public function messages(): array
    {
        return [
            'gallery.*.max' => 'Each gallery image must be 6 MB or smaller.',
            'cover.max' => 'The cover image must be 6 MB or smaller.',
            'brochure.max' => 'The brochure must be 8 MB or smaller.',
        ];
    }
}
