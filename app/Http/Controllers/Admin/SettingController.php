<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\HomeSlider;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const FILE_KEYS = ['logo', 'favicon'];

    public function index(Request $request): View
    {
        $groups = array_keys(SettingRequest::GROUPS);
        $tab = in_array($request->input('tab'), [...$groups, 'slider'], true) ? $request->input('tab') : 'general';

        return view('admin.settings.index', [
            'tab' => $tab,
            // One cached array for all settings — no query per field.
            'values' => Setting::values(),
            'sliders' => $tab === 'slider'
                ? HomeSlider::with(['media', 'tablet', 'mobile'])->orderBy('sort_order')->get()
                : collect(),
        ]);
    }

    public function update(SettingRequest $request, string $group): RedirectResponse
    {
        abort_unless(array_key_exists($group, SettingRequest::GROUPS), 404);

        foreach (array_keys(SettingRequest::GROUPS[$group]) as $key) {
            if (in_array($key, self::FILE_KEYS, true)) {
                if ($request->hasFile($key)) {
                    $this->replaceFile($key, $request->file($key)->store('settings', 'public'));
                }

                continue;
            }

            Setting::put($key, $request->validated($key), $group);
        }

        return redirect()
            ->route('admin.settings.index', ['tab' => $group])
            ->with('success', 'Settings saved. The website shows the change immediately.');
    }

    private function replaceFile(string $key, string $newPath): void
    {
        if ($old = Setting::get($key)) {
            Storage::disk('public')->delete($old);
        }

        Setting::put($key, $newPath, 'general', 'image');
    }
}
