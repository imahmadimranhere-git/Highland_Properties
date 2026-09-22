<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
    }

    public function create(): View
    {
        return view('admin.posts.create', ['post' => new Post()]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('cover');
        $data['author_id'] = $request->user()->id;
        $data['published_at'] = $data['published_at'] ?? ($data['is_published'] ? now() : null);

        $post = Post::create($data);
        $this->storeCover($request, $post);

        return redirect()->route('admin.content.index', ['tab' => 'posts'])->with('success', 'Post saved.');
    }

    public function edit(Post $post): View
    {
        $post->load('cover');

        return view('admin.posts.edit', compact('post'));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->safe()->except('cover');

        // First time a draft goes live, stamp it with today's date.
        if ($data['is_published'] && ! $post->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);
        $this->storeCover($request, $post);

        return redirect()->route('admin.posts.edit', $post)->with('success', 'Post saved.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return back()->with('success', 'Post removed.');
    }

    private function storeCover(PostRequest $request, Post $post): void
    {
        if ($request->hasFile('cover')) {
            $cover = $this->media->store($request->file('cover'), 'blog', $post->title);
            $post->update(['cover_media_id' => $cover->id]);
        }
    }
}
