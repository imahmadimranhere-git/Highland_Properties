@csrf

@if ($errors->any())
    <div class="alert alert--danger">Please correct the fields marked below.</div>
@endif

<div class="row">
    <div class="col-md-8 form-group">
        <label class="form-label" for="title">Title <span class="required">*</span></label>
        <input id="title" name="title" type="text" maxlength="180" required
               class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $report->title) }}">
        @error('title')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="type">Type <span class="required">*</span></label>
        <select id="type" name="type" class="form-select">
            @foreach ($types as $type)
                <option value="{{ $type->value }}" @selected(old('type', $report->type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="project_id">Project</label>
        <select id="project_id" name="project_id" class="form-select">
            <option value="">None</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected(old('project_id', $report->project_id) == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="amount">Amount</label>
        <input id="amount" name="amount" type="number" step="0.01" min="0"
               class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $report->amount) }}">
        @error('amount')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-4 form-group" data-when="single">
        <label class="form-label" for="report_date">Date</label>
        <input id="report_date" name="report_date" type="date" max="{{ today()->toDateString() }}"
               class="form-control @error('report_date') is-invalid @enderror"
               value="{{ old('report_date', $report->report_date?->toDateString()) }}">
        @error('report_date')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-4 form-group" data-when="period">
        <label class="form-label" for="period_start">Period start</label>
        <input id="period_start" name="period_start" type="date"
               class="form-control @error('period_start') is-invalid @enderror"
               value="{{ old('period_start', $report->period_start?->toDateString()) }}">
        @error('period_start')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-4 form-group" data-when="period">
        <label class="form-label" for="period_end">Period end</label>
        <input id="period_end" name="period_end" type="date"
               class="form-control @error('period_end') is-invalid @enderror"
               value="{{ old('period_end', $report->period_end?->toDateString()) }}">
        @error('period_end')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-12 form-group">
        <label class="form-label" for="description">Details</label>
        <textarea id="description" name="description" rows="5" maxlength="10000" class="form-control">{{ old('description', $report->description) }}</textarea>
    </div>

    <div class="col-md-8 form-group">
        <label class="form-label" for="attachment">Attachment</label>
        <input id="attachment" name="attachment" type="file" class="form-control @error('attachment') is-invalid @enderror">
        <span class="form-hint">Receipt, photo or document. Maximum 10 MB. Only you and the admin can open it.</span>
        @error('attachment')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="status">Save as</label>
        <select id="status" name="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $report->status?->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <span class="form-hint">Final reports can no longer be edited.</span>
    </div>
</div>

<div class="u-flex u-gap-8">
    <button type="submit" class="btn btn--primary">{{ $submit }}</button>
    <a href="{{ route('consultant.reports.index') }}" class="btn btn--secondary">Cancel</a>
</div>

@push('scripts')
<script>
    (function () {
        const type = document.getElementById('type');
        const sync = () => {
            const period = type.value === 'monthly_performance';
            document.querySelectorAll('[data-when="period"]').forEach((el) => { el.hidden = !period; });
            document.querySelectorAll('[data-when="single"]').forEach((el) => { el.hidden = period; });
        };
        type.addEventListener('change', sync);
        sync();
    })();
</script>
@endpush
