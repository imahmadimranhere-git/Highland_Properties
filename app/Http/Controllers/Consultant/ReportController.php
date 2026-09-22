<?php

namespace App\Http\Controllers\Consultant;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Consultant\ReportRequest;
use App\Models\Project;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * A consultant sees and edits only their own reports, found through
 * Report::visibleTo(). A report can be edited while it is a draft; once it
 * is marked final or reviewed by the admin, it is locked.
 */
class ReportController extends Controller
{
    private const DISK = 'local';

    public function index(Request $request): View
    {
        $reports = Report::visibleTo($request->user())
            ->with(['project:id,name', 'reviewer:id,name'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->latest('report_date')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('consultant.reports.index', [
            'reports' => $reports,
            'types' => ReportType::cases(),
        ]);
    }

    public function create(): View
    {
        return view('consultant.reports.create', $this->formData(new Report([
            'status' => ReportStatus::Draft,
            'report_date' => today(),
        ])));
    }

    public function store(ReportRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('attachment');
        $data['user_id'] = $request->user()->id;
        $data['attachment_path'] = $request->file('attachment')?->store('reports', self::DISK);

        Report::create($data);

        return redirect()->route('consultant.reports.index')->with('success', 'Report saved.');
    }

    public function edit(Request $request, int $report): View|RedirectResponse
    {
        $report = $this->find($request, $report);

        if (! $this->editable($report)) {
            return redirect()->route('consultant.reports.index')
                ->with('error', 'This report is final or already reviewed and can no longer be edited.');
        }

        return view('consultant.reports.edit', $this->formData($report));
    }

    public function update(ReportRequest $request, int $report): RedirectResponse
    {
        $report = $this->find($request, $report);
        abort_unless($this->editable($report), 403, 'This report is locked.');

        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            if ($report->attachment_path) {
                Storage::disk(self::DISK)->delete($report->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')->store('reports', self::DISK);
        }

        $report->update($data);

        return redirect()->route('consultant.reports.index')->with('success', 'Report updated.');
    }

    public function attachment(Request $request, int $report)
    {
        $report = $this->find($request, $report);

        abort_unless($report->attachment_path && Storage::disk(self::DISK)->exists($report->attachment_path), 404);

        return Storage::disk(self::DISK)->download($report->attachment_path);
    }

    private function find(Request $request, int $id): Report
    {
        return Report::visibleTo($request->user())->findOrFail($id);
    }

    private function editable(Report $report): bool
    {
        return $report->isEditable() && $report->reviewed_at === null;
    }

    private function formData(Report $report): array
    {
        return [
            'report' => $report,
            'types' => ReportType::cases(),
            'statuses' => ReportStatus::cases(),
            'projects' => Project::published()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
