<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\PublicCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/** The small content pages that each read one cached list. */
class PageController extends Controller
{
    public function about(): View
    {
        return view('web.about', [
            'team' => $this->teamMembers()->take(4),
        ]);
    }

    public function team(): View
    {
        return view('web.team', ['team' => $this->teamMembers()]);
    }

    public function testimonials(): View
    {
        $testimonials = Cache::remember(PublicCache::TESTIMONIALS_ALL, PublicCache::TTL, fn () => Testimonial::approved()
            ->with('project:id,name,slug')
            ->get(['id', 'project_id', 'name', 'designation', 'rating', 'message', 'photo']));

        return view('web.testimonials', compact('testimonials'));
    }

    public function faq(): View
    {
        $faqs = Cache::remember(PublicCache::FAQS, PublicCache::TTL, fn () => Faq::active()
            ->get(['id', 'question', 'answer', 'group'])
            ->groupBy('group'));

        return view('web.faq', compact('faqs'));
    }

    /** Active team members, cached. Shared by the About and Our Team pages. */
    private function teamMembers()
    {
        return Cache::remember(PublicCache::TEAM, PublicCache::TTL, fn () => TeamMember::active()
            ->get(['id', 'name', 'designation', 'photo', 'bio', 'phone', 'whatsapp', 'email', 'linkedin']));
    }
}
