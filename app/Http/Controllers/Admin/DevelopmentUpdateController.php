<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MediaCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DevelopmentUpdateRequest;
use App\Models\DevelopmentUpdate;
use App\Models\Project;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevelopmentUpdateController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    /** Every update across all projects, newest first. */
    public function index(Request $request): View
    {
        $updates = DevelopmentUpdate::query()
            ->with(['project:id,name', 'photos'])
            ->when($request->filled('project'), fn ($q) => $q->where('project_id', $request->integer('project')))
            ->orderByDesc('update_date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.development-updates.index', [
            'updates' => $updates,
            'projects' => Project::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** Timeline of one project, with the add form above it. */
    public function project(Project $project): View
    {
        $project->load(['developmentUpdates.photos']);

        return view('admin.development-updates.project', compact('project'));
    }

    public function store(DevelopmentUpdateRequest $request, Project $project): RedirectResponse
    {
        $update = $project->developmentUpdates()->create($request->safe()->except('photos'));
        $this->attachPhotos($request, $update);

        return back()->with('success', 'Update published to the project timeline.');
    }

    public function update(DevelopmentUpdateRequest $request, Project $project, DevelopmentUpdate $update): RedirectResponse
    {
        abort_unless($update->project_id === $project->id, 404);

        $update->update($request->safe()->except('photos'));
        $this->attachPhotos($request, $update);

        return back()->with('success', 'Update saved.');
    }

    public function destroy(Project $project, DevelopmentUpdate $update): RedirectResponse
    {
        abort_unless($update->project_id === $project->id, 404);

        $update->delete();

        return back()->with('success', 'Update removed.');
    }

    private function attachPhotos(DevelopmentUpdateRequest $request, DevelopmentUpdate $update): void
    {
        foreach ($request->file('photos', []) as $file) {
            $update->media()->attach(
                $this->media->store($file, 'updates', $update->title)->id,
                ['collection' => MediaCollection::UpdatePhoto->value]
            );
        }
    }
}
