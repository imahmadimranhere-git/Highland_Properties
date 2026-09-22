<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consultant\PasswordChangeRequest;
use App\Http\Requests\Consultant\ProfileRequest;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    public function edit(Request $request): View
    {
        return view('consultant.profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('avatar');

        if ($request->hasFile('avatar')) {
            $media = $this->media->store($request->file('avatar'), 'avatars', $data['name']);
            $data['avatar'] = $media->thumb_path ?: $media->path;
        }

        $request->user()->update($data);

        return back()->with('success', 'Profile saved.');
    }

    public function password(PasswordChangeRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['password' => $request->validated('password')]);

        // Signs out every other browser where this account was still logged in.
        Auth::logoutOtherDevices($request->validated('password'));

        return back()->with('success', 'Password changed. Other devices have been signed out.');
    }
}
