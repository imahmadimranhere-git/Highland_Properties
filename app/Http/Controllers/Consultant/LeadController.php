<?php

namespace App\Http\Controllers\Consultant;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadNoteRequest;
use App\Http\Requests\Admin\LeadStatusRequest;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SECURITY: leads are never resolved by implicit route-model binding here.
 * Every lookup goes through Lead::visibleTo($user), so a consultant who edits
 * the id in the URL to someone else's lead gets a 404 — the same response as
 * a lead that does not exist, which reveals nothing.
 */
class LeadController extends Controller
{
    public function __construct(private readonly LeadService $leads)
    {
    }

    public function index(Request $request): View
    {
        $leads = Lead::visibleTo($request->user())
            ->select(['id', 'name', 'phone', 'project_id', 'status', 'next_follow_up_at', 'created_at'])
            ->with(['project:id,name', 'society:id,name'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('phone', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->boolean('due'), fn ($q) => $q->open()->whereDate('next_follow_up_at', '<=', today()))
            ->when(! $request->filled('status') && ! $request->boolean('closed'), fn ($q) => $q->open())
            ->orderByRaw('next_follow_up_at IS NULL, next_follow_up_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('consultant.leads.index', [
            'leads' => $leads,
            'statuses' => LeadStatus::cases(),
        ]);
    }

    public function show(Request $request, int $lead): View
    {
        $lead = $this->find($request, $lead);

        $lead->load([
            'project:id,name,slug',
            'society:id,name,slug',
            'plotCategory:id,size_label,block,plot_type',
            'unitCategory:id,name,unit_type',
            'notes' => fn ($q) => $q->with('user:id,name'),
        ]);

        return view('consultant.leads.show', [
            'lead' => $lead,
            'statuses' => LeadStatus::cases(),
        ]);
    }

    public function status(LeadStatusRequest $request, int $lead): RedirectResponse
    {
        $this->leads->changeStatus(
            $this->find($request, $lead),
            LeadStatus::from($request->validated('status')),
            $request->user(),
            $request->validated('note'),
            $request->validated('deal_value') !== null ? (float) $request->validated('deal_value') : null,
        );

        return back()->with('success', 'Status updated.');
    }

    public function note(LeadNoteRequest $request, int $lead): RedirectResponse
    {
        $this->leads->addNote(
            $this->find($request, $lead),
            $request->user(),
            $request->validated('note'),
            $request->has('next_follow_up_at') ? (string) $request->validated('next_follow_up_at') : null,
        );

        return back()->with('success', 'Note saved.');
    }

    private function find(Request $request, int $id): Lead
    {
        return Lead::visibleTo($request->user())->findOrFail($id);
    }
}
