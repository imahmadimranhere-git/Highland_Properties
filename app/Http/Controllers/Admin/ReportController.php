<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportRequest;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Services\CsvExporter;
use App\Services\ReportSummaryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /** Attachments are internal documents, so they live on the private disk. */
    private const DISK = 'local';

    /** A PDF is rendered in memory; beyond this, CSV is the right format. */
    private const PDF_ROW_LIMIT = 300;

    public function index(Request $request): View
    {
        $reports = $this->filtered($request)
            ->with(['author:id,name', 'project:id,name', 'reviewer:id,name'])
            ->latest('report_date')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'types' => ReportType::cases(),
            'statuses' => ReportStatus::cases(),
            'people' => User::active()->orderBy('name')->get(['id', 'name']),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.reports.create', $this->formData(new Report([
            'status' => ReportStatus::Draft,
            'report_date' => today(),
        ])));
    }

    public function store(ReportRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('attachment');
        $data['attachment_path'] = $request->file('attachment')?->store('reports', self::DISK);

        Report::create($data);

        return redirect()->route('admin.reports.index')->with('success', 'Report saved.');
    }

    public function edit(Report $report): View
    {
        return view('admin.reports.edit', $this->formData($report));
    }

    public function update(ReportRequest $request, Report $report): RedirectResponse
    {
        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            if ($report->attachment_path) {
                Storage::disk(self::DISK)->delete($report->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')->store('reports', self::DISK);
        }

        $report->update($data);

        return redirect()->route('admin.reports.index')->with('success', 'Report updated.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();

        return back()->with('success', 'Report removed.');
    }

    /** Admin sign-off on a consultant's report. */
    public function review(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate(['review_note' => ['nullable', 'string', 'max:255']]);

        $report->update([
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $data['review_note'] ?? null,
        ]);

        return back()->with('success', 'Report marked as reviewed.');
    }

    /** Streams the private attachment only to a signed-in admin. */
    public function attachment(Report $report)
    {
        abort_unless($report->attachment_path && Storage::disk(self::DISK)->exists($report->attachment_path), 404);

        return Storage::disk(self::DISK)->download($report->attachment_path);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = $this->filtered($request)
            ->with(['author:id,name', 'project:id,name'])
            ->latest('report_date');

        return CsvExporter::stream(
            'reports-' . now()->format('Y-m-d') . '.csv',
            ['ID', 'Title', 'Type', 'Consultant', 'Project', 'Date', 'Period start', 'Period end',
             'Amount', 'Status', 'Reviewed', 'Description'],
            function ($write) use ($query) {
                foreach ($query->lazy(500) as $r) {
                    $write([
                        $r->id,
                        $r->title,
                        $r->type->label(),
                        $r->author?->name,
                        $r->project?->name,
                        $r->report_date?->format('Y-m-d'),
                        $r->period_start?->format('Y-m-d'),
                        $r->period_end?->format('Y-m-d'),
                        $r->amount,
                        $r->status->label(),
                        $r->reviewed_at ? 'Yes' : 'No',
                        $r->description,
                    ]);
                }
            }
        );
    }

    public function exportPdf(Request $request): Response|RedirectResponse
    {
        $query = $this->filtered($request)
            ->with(['author:id,name', 'project:id,name'])
            ->latest('report_date');

        if ((clone $query)->count() > self::PDF_ROW_LIMIT) {
            return back()->with('error', 'More than ' . self::PDF_ROW_LIMIT . ' reports match. Narrow the filters or use CSV export.');
        }

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'reports' => $query->get(),
            'filters' => $request->only(['type', 'status', 'user', 'project', 'from', 'to']),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reports-' . now()->format('Y-m-d') . '.pdf');
    }

    /** The four automatic reports for a chosen period. */
    public function summary(Request $request): View
    {
        $summary = ReportSummaryService::forPeriod($request->input('from'), $request->input('to'));

        return view('admin.reports.summary', [
            'summary' => $summary,
            'byStatus' => $summary->leadsByStatus(),
            'performance' => $summary->consultantPerformance(),
            'projects' => $summary->projectInquiries(),
            'sales' => $summary->monthlySales(),
        ]);
    }

    private function filtered(Request $request): Builder
    {
        return Report::query()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->integer('user')))
            ->when($request->filled('project'), fn ($q) => $q->where('project_id', $request->integer('project')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('report_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('report_date', '<=', $request->date('to')))
            ->when($request->input('reviewed') === 'no', fn ($q) => $q->whereNull('reviewed_at'));
    }

    private function formData(Report $report): array
    {
        return [
            'report' => $report,
            'types' => ReportType::cases(),
            'statuses' => ReportStatus::cases(),
            'people' => User::active()->orderBy('name')->get(['id', 'name']),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
        ];
    }
}
