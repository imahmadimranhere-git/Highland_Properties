<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeveloperRequest;
use App\Models\Developer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DeveloperController extends Controller
{
    public function index(Request $request): View
    {
        $developers = Developer::query()
            ->select(['id', 'name', 'slug', 'logo_path', 'experience_years', 'completed_projects', 'phone', 'is_active'])
            // withCount runs one extra aggregate query instead of one per row.
            ->withCount('projects')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->string('q') . '%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.developers.index', compact('developers'));
    }

    public function create(): View
    {
        return view('admin.developers.create', ['developer' => new Developer()]);
    }

    public function store(DeveloperRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['logo_path'] = $this->storeLogo($request);

        $developer = Developer::create($data);

        return redirect()
            ->route('admin.developers.index')
            ->with('success', "{$developer->name} was added.");
    }

    public function edit(Developer $developer): View
    {
        return view('admin.developers.edit', compact('developer'));
    }

    public function update(DeveloperRequest $request, Developer $developer): RedirectResponse
    {
        $data = $request->validated();

        if ($path = $this->storeLogo($request)) {
            $this->deleteLogo($developer);
            $data['logo_path'] = $path;
        }

        $developer->update($data);

        return redirect()
            ->route('admin.developers.index')
            ->with('success', "{$developer->name} was updated.");
    }

    public function destroy(Developer $developer): RedirectResponse
    {
        // Soft delete. A developer with projects is kept so the project
        // pages do not lose their developer section.
        $developer->delete();

        return redirect()
            ->route('admin.developers.index')
            ->with('success', "{$developer->name} was removed.");
    }

    private function storeLogo(DeveloperRequest $request): ?string
    {
        return $request->hasFile('logo')
            ? $request->file('logo')->store('developers', 'public')
            : null;
    }

    private function deleteLogo(Developer $developer): void
    {
        if ($developer->logo_path) {
            Storage::disk('public')->delete($developer->logo_path);
        }
    }
}
