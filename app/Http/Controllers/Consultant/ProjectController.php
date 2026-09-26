<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only. There is no create, update or delete route for projects in the
 * consultant route file, so prices cannot be changed even by a crafted request.
 */
class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $projects = Project::query()
            // Live projects, plus any unpublished one this consultant is assigned to.
            ->where(fn ($q) => $q->published()->orWhere('assigned_consultant_id', $userId))
            ->select(['id', 'name', 'slug', 'status', 'starting_price', 'city_id', 'location_id', 'cover_media_id', 'assigned_consultant_id', 'is_published'])
            ->with(['city:id,name', 'location:id,name', 'cover:id,disk,path,webp_path,thumb_path', 'coverTablet:id,disk,path,webp_path,thumb_path', 'coverMobile:id,disk,path,webp_path,thumb_path', 'locationImage:id,disk,path,webp_path,thumb_path,alt_text'])
            ->withCount('unitCategories')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->string('q') . '%'))
            ->orderByRaw('assigned_consultant_id = ? DESC', [$userId])
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('consultant.projects.index', compact('projects'));
    }

    public function show(Request $request, string $slug): View
    {
        $userId = $request->user()->id;

        $project = Project::query()
            ->where('slug', $slug)
            ->where(fn ($q) => $q->published()->orWhere('assigned_consultant_id', $userId))
            ->with([
                'developer:id,name',
                'city:id,name',
                'location:id,name',
                'projectType:id,name',
                'amenities:id,name',
                'unitCategories.paymentPlan',
                'developmentUpdates' => fn ($q) => $q->published()->with('photos'),
            ])
            ->firstOrFail();

        return view('consultant.projects.show', [
            'project' => $project,
            // The public page is built in step 6; the URL is stable from now on.
            'publicUrl' => url('/projects/' . $project->slug),
        ]);
    }
}
