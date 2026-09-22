<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeamMemberRequest;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    public function index(): View
    {
        $members = TeamMember::query()
            ->with('user:id,name')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.team.index', compact('members'));
    }

    public function create(): View
    {
        return view('admin.team.create', [
            'member' => new TeamMember(['is_active' => true]),
            'users' => User::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(TeamMemberRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $data['photo'] = $this->storePhoto($request);

        TeamMember::create($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member added.');
    }

    public function edit(TeamMember $team): View
    {
        return view('admin.team.edit', [
            'member' => $team,
            'users' => User::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(TeamMemberRequest $request, TeamMember $team): RedirectResponse
    {
        $data = $request->safe()->except('photo');

        if ($photo = $this->storePhoto($request)) {
            $data['photo'] = $photo;
        }

        $team->update($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $team): RedirectResponse
    {
        $team->delete();

        return back()->with('success', 'Team member removed.');
    }

    /**
     * Portraits are shown at card size only, so the 480px WebP thumbnail is
     * stored as the photo — there is no page that needs the full image.
     */
    private function storePhoto(TeamMemberRequest $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $media = $this->media->store($request->file('photo'), 'team', $request->input('name'));

        return $media->thumb_path ?: $media->path;
    }
}
