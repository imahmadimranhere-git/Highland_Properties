<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::published()
            ->with('cover:id,disk,path,webp_path,thumb_path')
            ->latest('published_at')
            ->paginate(9, ['id', 'title', 'slug', 'excerpt', 'cover_media_id', 'published_at']);

        return view('web.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['cover:id,disk,path,webp_path,thumb_path,width,height,alt_text', 'author:id,name'])
            ->firstOrFail();

        DB::table('posts')->where('id', $post->id)->increment('views_count');

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->with('cover:id,disk,path,webp_path,thumb_path')
            ->latest('published_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'cover_media_id', 'published_at']);

        return view('web.blog.show', compact('post', 'related'));
    }
}
