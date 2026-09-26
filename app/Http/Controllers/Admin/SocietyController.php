<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MediaCollection;
use App\Enums\OwnershipFlag;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocietyRequest;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Developer;
use App\Models\Location;
use App\Models\Media;
use App\Models\Society;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SocietyController extends Controller
{
    private const FILE_FIELDS = [
        'amenities', 'cover', 'cover_tablet', 'cover_mobile', 'location_image', 'gallery', 'master_plans', 'brochure',
    ];

    private const COVER_CROPS = [
        'cover' => 'cover_media_id',
        'cover_tablet' => 'cover_media_id_tablet',
        'cover_mobile' => 'cover_media_id_mobile',
        'location_image' => 'location_media_id',
    ];

    public function __construct(private readonly MediaService $media)
    {
    }

    public function index(Request $request): View
    {
        $societies = Society::query()
            ->select([
                'id', 'name', 'slug', 'status', 'starting_price', 'total_plots',
                'developer_id', 'city_id', 'cover_media_id', 'cover_media_id_tablet',
                'cover_media_id_mobile', 'is_featured', 'is_published', 'updated_at',
            ])
            ->with(['developer:id,name', 'city:id,name', 'cover:id,disk,path,webp_path,thumb_path'])
            ->withCount(['plotCategories', 'leads'])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->string('q') . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderBy('sort_order')
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.societies.index', [
            'societies' => $societies,
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.societies.create', $this->formData(new Society()));
    }

    public function store(SocietyRequest $request): RedirectResponse
    {
        $society = Society::create($request->safe()->except(self::FILE_FIELDS));
        $this->syncRelations($request, $society);

        return redirect()
            ->route('admin.societies.plots.index', $society)
            ->with('success', "{$society->name} was created. Add its plot sizes next.");
    }

    public function edit(Society $society): View
    {
        $society->load([
            'amenities:id',
            'cover:id,disk,path,webp_path,thumb_path',
            'coverTablet:id,disk,path,webp_path,thumb_path',
            'coverMobile:id,disk,path,webp_path,thumb_path',
            'media',
        ]);

        return view('admin.societies.edit', $this->formData($society));
    }

    public function update(SocietyRequest $request, Society $society): RedirectResponse
    {
        $society->update($request->safe()->except(self::FILE_FIELDS));
        $this->syncRelations($request, $society);

        return redirect()->route('admin.societies.edit', $society)->with('success', 'Society saved.');
    }

    public function destroy(Society $society): RedirectResponse
    {
        $society->delete();

        return redirect()->route('admin.societies.index')->with('success', "{$society->name} was removed.");
    }

    public function detachMedia(Society $society, Media $media): RedirectResponse
    {
        $society->media()->detach($media->id);

        foreach (self::COVER_CROPS as $column) {
            if ($society->{$column} === $media->id) {
                $society->update([$column => null]);
            }
        }

        $stillUsed = DB::table('mediables')->where('media_id', $media->id)->exists();

        if (! $stillUsed) {
            $this->media->delete($media);
        }

        return back()->with('success', 'Image removed.');
    }

    private function formData(Society $society): array
    {
        return [
            'society' => $society,
            'developers' => Developer::active()->orderBy('name')->get(['id', 'name']),
            'cities' => City::active()->orderBy('name')->get(['id', 'name']),
            'locations' => Location::active()->orderBy('name')->get(['id', 'city_id', 'name']),
            'consultants' => User::consultants()->active()->orderBy('name')->get(['id', 'name']),
            'amenities' => Amenity::active()->orderBy('name')->get(['id', 'name']),
            'statuses' => ProjectStatus::cases(),
            'ownerships' => OwnershipFlag::cases(),
        ];
    }

    private function syncRelations(SocietyRequest $request, Society $society): void
    {
        $society->amenities()->sync($request->input('amenities', []));

        $covers = [];

        foreach (self::COVER_CROPS as $field => $column) {
            if ($request->hasFile($field)) {
                $covers[$column] = $this->media->store($request->file($field), 'societies', $society->name)->id;
            }
        }

        if ($covers) {
            $society->update($covers);
        }

        foreach ($request->file('gallery', []) as $file) {
            $society->media()->attach(
                $this->media->store($file, 'societies', $society->name)->id,
                ['collection' => MediaCollection::Gallery->value]
            );
        }

        // Master plans share the floor-plan collection: same purpose, a layout drawing.
        foreach ($request->file('master_plans', []) as $file) {
            $society->media()->attach(
                $this->media->store($file, 'societies/plans', $society->name . ' master plan')->id,
                ['collection' => MediaCollection::FloorPlan->value]
            );
        }

        if ($request->hasFile('brochure')) {
            if ($society->brochure_path) {
                Storage::disk('public')->delete($society->brochure_path);
            }

            $society->update(['brochure_path' => $request->file('brochure')->store('brochures', 'public')]);
        }
    }
}
