<?php

namespace App\Http\Requests\Admin;

use App\Enums\OwnershipFlag;
use App\Enums\ProjectStatus;
use App\Http\Requests\Concerns\DefaultsSortOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocietyRequest extends FormRequest
{
    use DefaultsSortOrder;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('society')?->id;
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'];

        return [
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash', Rule::unique('societies', 'slug')->ignore($id)],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'assigned_consultant_id' => ['nullable', 'exists:users,id'],

            'ownership_flag' => ['required', Rule::enum(OwnershipFlag::class)],
            'status' => ['required', Rule::enum(ProjectStatus::class)],

            'short_description' => ['nullable', 'string', 'max:320'],
            'description' => ['nullable', 'string', 'max:20000'],

            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            // Google embed links are 600–900 characters, and admins often paste
            // the whole <iframe> block, so this accepts text and the src is
            // pulled out when the page is rendered.
            'map_embed_url' => ['nullable', 'string', 'max:2000'],
            'nearby_landmarks' => ['nullable', 'array', 'max:12'],
            'nearby_landmarks.*' => ['string', 'max:160'],

            'total_area' => ['nullable', 'string', 'max:80'],
            'total_plots' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'noc_status' => ['nullable', 'string', 'max:160'],
            'development_charges' => ['nullable', 'string', 'max:160'],
            'possession_target' => ['nullable', 'date'],

            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],

            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],

            'cover' => ['nullable', ...$image],
            'cover_tablet' => ['nullable', ...$image],
            'cover_mobile' => ['nullable', ...$image],
            'gallery' => ['nullable', 'array', 'max:24'],
            'gallery.*' => $image,
            'master_plans' => ['nullable', 'array', 'max:12'],
            'master_plans.*' => $image,
            'brochure' => ['nullable', 'file', 'mimes:pdf', 'max:8192'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            ...$this->defaultSortOrder(),
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
            'nearby_landmarks' => collect(preg_split('/\r\n|\r|\n/', (string) $this->input('landmarks_text')))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all(),
        ]);
    }

    /** A society goes live only with all three cover crops, like a project. */
    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator) {
                if (! $this->boolean('is_published')) {
                    return;
                }

                $society = $this->route('society');

                foreach ([
                    'cover' => $society?->cover_media_id,
                    'cover_tablet' => $society?->cover_media_id_tablet,
                    'cover_mobile' => $society?->cover_media_id_mobile,
                ] as $field => $existing) {
                    if (! $existing && ! $this->hasFile($field)) {
                        $validator->errors()->add($field, 'Upload the ' . $this->attributes()[$field] . ' before putting the society live.');
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
            'total_area' => 'total area',
        ];
    }
}
