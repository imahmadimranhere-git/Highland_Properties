<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\InquiryRequest;
use App\Models\Project;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function __construct(private readonly LeadService $leads)
    {
    }

    /**
     * Saves the inquiry as a Lead. LeadService assigns it to the project's
     * consultant, so it shows up on their dashboard straight away.
     */
    public function store(InquiryRequest $request): RedirectResponse
    {
        $this->leads->createFromWebsite($request->safe()->except('website'));

        $slug = Project::whereKey($request->integer('project_id'))->value('slug');

        return redirect()
            ->to(route('projects.show', $slug) . '#inquiry')
            ->with('inquiry_sent', true);
    }
}
