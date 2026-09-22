<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TestimonialStatus;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Module 11 landing page: blog, testimonials and FAQs as three tabs. */
class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $tab = in_array($request->input('tab'), ['posts', 'testimonials', 'faqs'], true)
            ? $request->input('tab')
            : 'posts';

        $data = match ($tab) {
            'testimonials' => [
                'testimonials' => Testimonial::with('project:id,name')
                    ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
                    ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
                    ->orderBy('sort_order')
                    ->paginate(20)
                    ->withQueryString(),
                'pendingCount' => Testimonial::where('status', TestimonialStatus::Pending->value)->count(),
            ],
            'faqs' => ['faqs' => Faq::orderBy('group')->orderBy('sort_order')->paginate(30)->withQueryString()],
            default => [
                'posts' => Post::with('author:id,name')
                    ->select(['id', 'author_id', 'title', 'slug', 'is_published', 'published_at', 'views_count', 'updated_at'])
                    ->latest('updated_at')
                    ->paginate(20)
                    ->withQueryString(),
            ],
        };

        return view('admin.content.index', ['tab' => $tab] + $data);
    }
}
