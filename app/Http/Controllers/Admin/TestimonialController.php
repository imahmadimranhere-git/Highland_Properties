<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TestimonialStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Project;
use App\Models\Testimonial;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    public function create(): View
    {
        return view('admin.testimonials.create', $this->formData(new Testimonial([
            'rating' => 5,
            'status' => TestimonialStatus::Approved,
        ])));
    }

    public function store(TestimonialRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $data['photo'] = $this->storePhoto($request);

        Testimonial::create($data);

        return redirect()->route('admin.content.index', ['tab' => 'testimonials'])->with('success', 'Testimonial added.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.edit', $this->formData($testimonial));
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $request->safe()->except('photo');

        if ($photo = $this->storePhoto($request)) {
            $data['photo'] = $photo;
        }

        $testimonial->update($data);

        return redirect()->route('admin.content.index', ['tab' => 'testimonials'])->with('success', 'Testimonial updated.');
    }

    /** One-click approve / reject from the list. */
    public function moderate(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([TestimonialStatus::Approved->value, TestimonialStatus::Rejected->value])],
        ]);

        $testimonial->update($data);

        return back()->with('success', 'Testimonial ' . $data['status'] . '.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return back()->with('success', 'Testimonial removed.');
    }

    private function formData(Testimonial $testimonial): array
    {
        return [
            'testimonial' => $testimonial,
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'statuses' => TestimonialStatus::cases(),
        ];
    }

    private function storePhoto(TestimonialRequest $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $media = $this->media->store($request->file('photo'), 'testimonials', $request->input('name'));

        return $media->thumb_path ?: $media->path;
    }
}
