<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\InquiryRequest;
use App\Models\Project;
use App\Models\Society;
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

        $target = $request->filled('society_id')
            ? route('societies.show', Society::whereKey($request->integer('society_id'))->value('slug'))
            : route('projects.show', Project::whereKey($request->integer('project_id'))->value('slug'));

        return redirect()->to($target . '#inquiry')->with('inquiry_sent', true);
    }
}
