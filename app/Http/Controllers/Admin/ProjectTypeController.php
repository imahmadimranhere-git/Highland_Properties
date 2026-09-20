<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectTypeRequest;
use App\Models\ProjectType;
use Illuminate\Http\RedirectResponse;

class ProjectTypeController extends Controller
{
    public function store(ProjectTypeRequest $request): RedirectResponse
    {
        ProjectType::create($request->validated());

        return back()->with('success', 'Project type added.');
    }

    public function update(ProjectTypeRequest $request, ProjectType $projectType): RedirectResponse
    {
        $projectType->update($request->validated());

        return back()->with('success', 'Project type updated.');
    }

    public function destroy(ProjectType $projectType): RedirectResponse
    {
        if ($projectType->projects()->exists()) {
            return back()->with('error', 'This type is used by projects. Deactivate it instead.');
        }

        $projectType->delete();

        return back()->with('success', 'Project type removed.');
    }
}
