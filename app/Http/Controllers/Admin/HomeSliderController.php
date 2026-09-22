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
    /** The public home page caches its slides under this key (step 6). */
    public const CACHE_KEY = 'home.sliders';

    public function __construct(private readonly MediaService $media)
    {
    }

    public function store(HomeSliderRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['media_id'] = $this->media->store($request->file('image'), 'slider', $data['title'] ?? null)->id;

        HomeSlider::create($data);
        Cache::forget(self::CACHE_KEY);

        return back()->with('success', 'Slide added.');
    }

    public function update(HomeSliderRequest $request, HomeSlider $slider): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['media_id'] = $this->media->store($request->file('image'), 'slider', $data['title'] ?? null)->id;
        }

        $slider->update($data);
        Cache::forget(self::CACHE_KEY);

        return back()->with('success', 'Slide updated.');
    }

    public function destroy(HomeSlider $slider): RedirectResponse
    {
        $slider->delete();
        Cache::forget(self::CACHE_KEY);

        return back()->with('success', 'Slide removed.');
    }
}
