<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only, exactly like the projects screen: the consultant route file has
 * no create, update or delete route for societies, so plot rates cannot be
 * changed from here even by a crafted request.
 *
 * Every live society is listed — a consultant is often asked about a scheme
 * that is not theirs — but the ones assigned to them come first.
 */
class SocietyController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $societies = Society::query()
            ->where(fn ($q) => $q->published()->orWhere('assigned_consultant_id', $userId))
            ->select([
                'id', 'name', 'slug', 'status', 'starting_price', 'total_plots', 'city_id',
                'location_id', 'cover_media_id', 'cover_media_id_tablet', 'cover_media_id_mobile',
                'assigned_consultant_id', 'is_published',
            ])
            ->with([
                'city:id,name',
                'location:id,name',
                'cover:id,disk,path,webp_path,thumb_path',
                'coverTablet:id,disk,path,webp_path,thumb_path',
                'coverMobile:id,disk,path,webp_path,thumb_path',
            ])
            ->withCount('plotCategories')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->string('q') . '%'))
            ->orderByRaw('assigned_consultant_id = ? DESC', [$userId])
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('consultant.societies.index', compact('societies'));
    }

    public function show(Request $request, string $slug): View
    {
        $userId = $request->user()->id;

        $society = Society::query()
            ->where('slug', $slug)
            ->where(fn ($q) => $q->published()->orWhere('assigned_consultant_id', $userId))
            ->with([
                'developer:id,name',
                'city:id,name',
                'location:id,name',
                'amenities:id,name',
                'plotCategories',
            ])
            ->firstOrFail();

        return view('consultant.societies.show', [
            'society' => $society,
            'publicUrl' => url('/societies/' . $society->slug),
        ]);
    }
}
