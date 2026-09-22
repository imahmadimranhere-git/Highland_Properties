<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use App\Models\Media;
use App\Models\Post;
use App\Models\Project;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    public function index(Request $request): View
    {
        $items = Media::query()
            ->select(['id', 'disk', 'path', 'webp_path', 'thumb_path', 'original_name', 'mime', 'size', 'width', 'height', 'alt_text', 'folder', 'created_at'])
            ->when($request->filled('folder'), fn ($q) => $q->where('folder', $request->string('folder')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('original_name', 'like', '%' . $request->string('q') . '%')
                ->orWhere('alt_text', 'like', '%' . $request->string('q') . '%')))
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('admin.media.index', [
            'items' => $items,
            'folders' => Media::query()->whereNotNull('folder')->distinct()->orderBy('folder')->pluck('folder'),
            'totalSize' => (int) Media::sum('size'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,webp,svg,pdf', 'max:6144'],
        ], [
            'files.*.max' => 'Each file must be 6 MB or smaller.',
        ]);

        foreach ($request->file('files') as $file) {
            $this->media->store($file, 'library');
        }

        return back()->with('success', count($request->file('files')) . ' file(s) uploaded.');
    }

    public function update(Request $request, Media $medium): RedirectResponse
    {
        $data = $request->validate(['alt_text' => ['nullable', 'string', 'max:255']]);
        $medium->update($data);

        return back()->with('success', 'Description saved.');
    }

    /** A file still used on the website is never deleted from here. */
    public function destroy(Media $medium): RedirectResponse
    {
        $inUse = DB::table('mediables')->where('media_id', $medium->id)->exists()
            || Project::where('cover_media_id', $medium->id)->exists()
            || Post::where('cover_media_id', $medium->id)->exists()
            || HomeSlider::where('media_id', $medium->id)->exists();

        if ($inUse) {
            return back()->with('error', 'This file is used on the website. Remove it from the project, post or slide first.');
        }

        $this->media->delete($medium);

        return back()->with('success', 'File deleted.');
    }
}
