<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadNoteRequest;
use App\Http\Requests\Admin\LeadRequest;
use App\Http\Requests\Admin\LeadStatusRequest;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use App\Services\CsvExporter;
use App\Services\DashboardStatsService;
use App\Services\LeadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    /** A PDF is rendered in memory; beyond this, CSV is the right format. */
    private const PDF_ROW_LIMIT = 400;

    public function __construct(private readonly LeadService $leads)
    {
    }

    public function index(Request $request): View
    {
        $leads = $this->filtered($request)
            ->select([
                'id', 'name', 'phone', 'email', 'project_id', 'assigned_to',
                'status', 'source', 'next_follow_up_at', 'created_at',
            ])
            ->with(['project:id,name', 'society:id,name', 'assignedTo:id,name'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'statuses' => LeadStatus::cases(),
            'consultants' => $this->consultants(),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.leads.create', [
            'lead' => new Lead(['source' => LeadSource::Manual]),
            'consultants' => $this->consultants(),
            'projects' => Project::with('unitCategories:id,project_id,name,unit_type')->orderBy('name')->get(['id', 'name']),
            'sources' => LeadSource::cases(),
        ]);
    }

    public function store(LeadRequest $request): RedirectResponse
    {
        $lead = Lead::create($request->validated() + ['status' => LeadStatus::New]);

        $lead->notes()->create([
            'user_id' => $request->user()->id,
            'note' => 'Lead entered manually.',
            'status_to' => LeadStatus::New->value,
        ]);

        DashboardStatsService::flush();

        return redirect()->route('admin.leads.show', $lead)->with('success', 'Lead created.');
    }

    public function show(Lead $lead): View
    {
        $lead->load([
            'project:id,name,slug',
            'society:id,name,slug',
            'plotCategory:id,size_label,block,plot_type',
            'unitCategory:id,name,unit_type',
            'assignedTo:id,name,phone',
            'notes' => fn ($q) => $q->with('user:id,name'),
        ]);

        return view('admin.leads.show', [
            'lead' => $lead,
            'statuses' => LeadStatus::cases(),
            'consultants' => $this->consultants(),
        ]);
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();
        DashboardStatsService::flush();

        return redirect()->route('admin.leads.index')->with('success', 'Lead removed.');
    }

    public function assign(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $this->leads->assign($lead, $data['assigned_to'] ?? null, $request->user());

        return back()->with('success', 'Assignment updated.');
    }

    public function status(LeadStatusRequest $request, Lead $lead): RedirectResponse
    {
        $this->leads->changeStatus(
            $lead,
            LeadStatus::from($request->validated('status')),
            $request->user(),
            $request->validated('note'),
            $request->validated('deal_value') !== null ? (float) $request->validated('deal_value') : null,
        );

        return back()->with('success', 'Status changed.');
    }

    public function note(LeadNoteRequest $request, Lead $lead): RedirectResponse
    {
        $this->leads->addNote(
            $lead,
            $request->user(),
            $request->validated('note'),
            $request->has('next_follow_up_at') ? (string) $request->validated('next_follow_up_at') : null,
        );

        return back()->with('success', 'Note saved.');
    }

    /**
     * Same filters as the list, rendered as a PDF the admin can print or
     * send on. Capped because a PDF is built in memory, unlike the CSV.
     */
    public function exportPdf(Request $request): Response|RedirectResponse
    {
        $query = $this->filtered($request)
            ->with(['project:id,name', 'unitCategory:id,name', 'assignedTo:id,name'])
            ->latest();

        if ((clone $query)->count() > self::PDF_ROW_LIMIT) {
            return back()->with('error', 'More than ' . self::PDF_ROW_LIMIT . ' leads match these filters. Narrow the date range, or use the CSV export.');
        }

        $leads = $query->get();

        $pdf = Pdf::loadView('admin.leads.pdf', [
            'leads' => $leads,
            'filters' => $this->filterSummary($request),
            'wonValue' => $leads->where('status', LeadStatus::ClosedWon)->sum('deal_value'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('leads-' . now()->format('Y-m-d') . '.pdf');
    }

    /** Readable description of the filters, printed on the PDF. */
    private function filterSummary(Request $request): array
    {
        $summary = [];

        if ($request->filled('q')) {
            $summary[] = 'Search: ' . $request->string('q');
        }

        if ($request->filled('status')) {
            $summary[] = 'Status: ' . LeadStatus::from($request->string('status')->toString())->label();
        }

        if ($request->filled('project')) {
            $summary[] = 'Project: ' . Project::whereKey($request->integer('project'))->value('name');
        }

        if ($request->input('consultant') === 'none') {
            $summary[] = 'Consultant: unassigned';
        } elseif ($request->filled('consultant')) {
            $summary[] = 'Consultant: ' . User::whereKey($request->integer('consultant'))->value('name');
        }

        if ($request->filled('from') || $request->filled('to')) {
            $summary[] = 'Received: ' . ($request->input('from') ?: 'any') . ' to ' . ($request->input('to') ?: 'today');
        }

        if ($request->boolean('due')) {
            $summary[] = 'Follow-up due or overdue';
        }

        return $summary;
    }

    /** Same filters as the list, streamed as CSV. */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = $this->filtered($request)
            ->with(['project:id,name', 'assignedTo:id,name', 'unitCategory:id,name'])
            ->latest();

        return CsvExporter::stream(
            'leads-' . now()->format('Y-m-d') . '.csv',
            ['ID', 'Name', 'Phone', 'Email', 'Project', 'Category', 'Consultant', 'Status',
             'Source', 'Next follow-up', 'Deal value', 'Received'],
            function ($write) use ($query) {
                // lazy() reads 500 rows at a time instead of loading every lead.
                foreach ($query->lazy(500) as $lead) {
                    $write([
                        $lead->id,
                        $lead->name,
                        $lead->phone,
                        $lead->email,
                        $lead->project?->name,
                        $lead->unitCategory?->name,
                        $lead->assignedTo?->name,
                        $lead->status->label(),
                        $lead->source->label(),
                        $lead->next_follow_up_at?->format('Y-m-d'),
                        $lead->deal_value,
                        $lead->created_at->format('Y-m-d H:i'),
                    ]);
                }
            }
        );
    }

    /** Filter logic shared by the list and the export. */
    private function filtered(Request $request): Builder
    {
        return Lead::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('project'), fn ($q) => $q->where('project_id', $request->integer('project')))
            ->when($request->input('consultant') === 'none', fn ($q) => $q->whereNull('assigned_to'))
            ->when(
                $request->filled('consultant') && $request->input('consultant') !== 'none',
                fn ($q) => $q->where('assigned_to', $request->integer('consultant'))
            )
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->boolean('due'), fn ($q) => $q->open()->whereDate('next_follow_up_at', '<=', today()));
    }

    private function consultants()
    {
        return User::consultants()->active()->orderBy('name')->get(['id', 'name']);
    }
}
