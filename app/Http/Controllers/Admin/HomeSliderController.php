<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSliderRequest;
use App\Models\HomeSlider;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class HomeSliderController extends Controller
{
    /** The public home page caches its slides under this key. */
    public const CACHE_KEY = \App\Support\PublicCache::SLIDERS;

    /** Form field => database column. */
    private const CROPS = [
        'image' => 'media_id',
        'image_tablet' => 'media_id_tablet',
        'image_mobile' => 'media_id_mobile',
    ];

    public function __construct(private readonly MediaService $media)
    {
    }

    public function store(HomeSliderRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(array_keys(self::CROPS));

        HomeSlider::create($data + $this->storeCrops($request, $data['title'] ?? null));
        Cache::forget(self::CACHE_KEY);

        return back()->with('success', 'Banner added with all three sizes.');
    }

    public function update(HomeSliderRequest $request, HomeSlider $slider): RedirectResponse
    {
        $data = $request->safe()->except(array_keys(self::CROPS));

        $slider->update($data + $this->storeCrops($request, $data['title'] ?? $slider->title));
        Cache::forget(self::CACHE_KEY);

        $message = $slider->fresh()->isComplete()
            ? 'Banner updated.'
            : 'Saved, but the banner stays off the website until the ' . implode(' and ', $slider->fresh()->missingSizes()) . ' image is uploaded.';

        return back()->with($slider->fresh()->isComplete() ? 'success' : 'error', $message);
    }

    public function destroy(HomeSlider $slider): RedirectResponse
    {
        $slider->delete();
        Cache::forget(self::CACHE_KEY);

        return back()->with('success', 'Banner removed.');
    }

    /**
     * Stores whichever crops were uploaded and returns the columns to set.
     * Each crop is converted to WebP like any other upload.
     */
    private function storeCrops(HomeSliderRequest $request, ?string $title): array
    {
        $columns = [];

        foreach (self::CROPS as $field => $column) {
            if ($request->hasFile($field)) {
                $columns[$column] = $this->media->store($request->file($field), 'slider', $title)->id;
            }
        }

        return $columns;
    }
}
