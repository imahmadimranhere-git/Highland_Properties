<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MediaCollection;
use App\Enums\OwnershipFlag;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Developer;
use App\Models\Location;
use App\Models\Media;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use App\Services\DashboardStatsService;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    public function index(Request $request): View
    {
        $projects = Project::query()
            ->select([
                'id', 'name', 'slug', 'status', 'ownership_flag', 'starting_price',
                'developer_id', 'city_id', 'cover_media_id', 'assigned_consultant_id',
                'is_featured', 'is_published', 'updated_at',
            ])
            // One query per relation instead of one per row.
            ->with([
                'developer:id,name',
                'city:id,name',
                'consultant:id,name',
                'cover:id,disk,path,webp_path,thumb_path',
            ])
            ->withCount('leads')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->string('q') . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('developer'), fn ($q) => $q->where('developer_id', $request->integer('developer')))
            ->when($request->filled('published'), fn ($q) => $q->where('is_published', $request->boolean('published')))
            ->orderBy('sort_order')
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'developers' => Developer::orderBy('name')->get(['id', 'name']),
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.create', $this->formData(new Project()));
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = Project::create($request->safe()->except(['amenities', 'cover', 'gallery', 'floor_plans', 'brochure']));

        $this->syncRelations($request, $project);
        DashboardStatsService::flush();

        return redirect()
            ->route('admin.projects.categories.index', $project)
            ->with('success', "{$project->name} was created. Add its unit categories next.");
    }

    public function edit(Project $project): View
    {
        $project->load([
            'amenities:id',
            'cover:id,disk,path,webp_path,thumb_path',
            'media',
        ]);

        return view('admin.projects.edit', $this->formData($project));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->safe()->except(['amenities', 'cover', 'gallery', 'floor_plans', 'brochure']));

        $this->syncRelations($request, $project);
        DashboardStatsService::flush();

        return redirect()
            ->route('admin.projects.edit', $project)
            ->with('success', 'Project saved.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        // Soft delete: leads, reports and past inquiries keep their reference.
        $project->delete();
        DashboardStatsService::flush();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', "{$project->name} was removed.");
    }

    /** Removes one gallery image or floor plan from a project. */
    public function detachMedia(Project $project, Media $media): RedirectResponse
    {
        $project->media()->detach($media->id);

        if ($project->cover_media_id === $media->id) {
            $project->update(['cover_media_id' => null]);
        }

        // The file itself is only deleted when nothing else points at it.
        $stillUsed = DB::table('mediables')->where('media_id', $media->id)->exists()
            || Project::where('cover_media_id', $media->id)->exists();

        if (! $stillUsed) {
            $this->media->delete($media);
        }

        return back()->with('success', 'Image removed.');
    }

    private function formData(Project $project): array
    {
        return [
            'project' => $project,
            'developers' => Developer::active()->orderBy('name')->get(['id', 'name']),
            'cities' => City::active()->orderBy('name')->get(['id', 'name']),
            'locations' => Location::active()->orderBy('name')->get(['id', 'city_id', 'name']),
            'types' => ProjectType::active()->orderBy('name')->get(['id', 'name']),
            'consultants' => User::consultants()->active()->orderBy('name')->get(['id', 'name']),
            'amenities' => Amenity::active()->orderBy('name')->get(['id', 'name']),
            'statuses' => ProjectStatus::cases(),
            'ownerships' => OwnershipFlag::cases(),
        ];
    }

    private function syncRelations(ProjectRequest $request, Project $project): void
    {
        $project->amenities()->sync($request->input('amenities', []));

        if ($request->hasFile('cover')) {
            $cover = $this->media->store($request->file('cover'), 'projects', $project->name);
            $project->update(['cover_media_id' => $cover->id]);
        }

        foreach ($request->file('gallery', []) as $file) {
            $project->media()->attach(
                $this->media->store($file, 'projects', $project->name)->id,
                ['collection' => MediaCollection::Gallery->value]
            );
        }

        foreach ($request->file('floor_plans', []) as $file) {
            $project->media()->attach(
                $this->media->store($file, 'projects/plans', $project->name . ' floor plan')->id,
                ['collection' => MediaCollection::FloorPlan->value]
            );
        }

        if ($request->hasFile('brochure')) {
            if ($project->brochure_path) {
                Storage::disk('public')->delete($project->brochure_path);
            }

            $project->update([
                'brochure_path' => $request->file('brochure')->store('brochures', 'public'),
            ]);
        }
    }
}
