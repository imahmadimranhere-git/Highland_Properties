@extends('layouts.admin')

@section('title', 'Lead')

@section('content')
    <x-panel.page-head :title="$lead->name" :sub="'Received ' . $lead->created_at->format('d M Y, h:i A') . ' via ' . $lead->source->label()">
        <x-slot:actions>
            <a href="tel:{{ $lead->phone }}" class="btn btn--secondary btn--sm"><x-ui.icon name="phone" :size="16" /> Call</a>
            <a href="{{ $lead->whatsapp_link }}" target="_blank" rel="noopener" class="btn btn--secondary btn--sm">WhatsApp</a>
            <a href="{{ route('admin.leads.index') }}" class="btn btn--secondary btn--sm">All leads</a>
        </x-slot:actions>
    </x-panel.page-head>

    @if ($errors->any())
        <div class="alert alert--danger">{{ $errors->first() }}</div>
    @endif

    <div class="row">
        <div class="col-lg-5">
            <x-panel.box title="Client">
                <dl class="detail-list">
                    <dt>Phone</dt><dd>{{ $lead->phone }}</dd>
                    <dt>Email</dt><dd>{{ $lead->email ?: '—' }}</dd>
                    <dt>Project</dt><dd>{{ $lead->project?->name ?? $lead->society?->name ?? 'General inquiry' }}</dd>
                    <dt>Category</dt><dd>{{ $lead->unitCategory?->name ?? $lead->plotCategory?->size_label ?? '—' }}</dd>
                    <dt>Status</dt><dd><x-ui.status-badge :status="$lead->status" /></dd>
                    <dt>Follow-up</dt><dd>{{ $lead->next_follow_up_at?->format('d M Y') ?? '—' }}</dd>
                    @if ($lead->deal_value)
                        <dt>Deal value</dt><dd class="is-price">{{ money($lead->deal_value) }}</dd>
                    @endif
                </dl>

                @if ($lead->message)
                    <hr class="rule-gold" style="margin:18px 0;">
                    <p class="section-label">Their message</p>
                    <p class="u-mb-0">{{ $lead->message }}</p>
                @endif
            </x-panel.box>

            <x-panel.box title="Assignment">
                <form method="POST" action="{{ route('admin.leads.assign', $lead) }}" class="u-flex u-gap-8">
                    @csrf @method('PATCH')
                    <select name="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        @foreach ($consultants as $consultant)
                            <option value="{{ $consultant->id }}" @selected($lead->assigned_to === $consultant->id)>{{ $consultant->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn--primary btn--sm">Save</button>
                </form>
            </x-panel.box>

            <x-panel.box title="Change status">
                <form method="POST" action="{{ route('admin.leads.status', $lead) }}" novalidate>
                    @csrf @method('PATCH')

                    <div class="form-group">
                        <select name="status" id="status" class="form-select">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($lead->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="deal-value-group">
                        <label class="form-label" for="deal_value">Deal value (for won deals)</label>
                        <input id="deal_value" name="deal_value" type="number" step="0.01" min="0"
                               class="form-control @error('deal_value') is-invalid @enderror"
                               value="{{ old('deal_value', $lead->deal_value) }}">
                        @error('deal_value')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status-note">Note</label>
                        <textarea id="status-note" name="note" rows="2" maxlength="2000" class="form-control"
                                  placeholder="Why the status changed"></textarea>
                    </div>

                    <button class="btn btn--primary btn--sm">Update status</button>
                </form>
            </x-panel.box>
        </div>

        <div class="col-lg-7">
            <x-panel.box title="Add a note">
                <form method="POST" action="{{ route('admin.leads.note', $lead) }}" novalidate>
                    @csrf

                    <div class="form-group">
                        <textarea name="note" rows="3" maxlength="2000"
                                  class="form-control @error('note') is-invalid @enderror"
                                  placeholder="What was discussed">{{ old('note') }}</textarea>
                        @error('note')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="u-flex u-gap-8 u-wrap" style="align-items:flex-end;">
                        <div class="form-group u-mb-0">
                            <label class="form-label" for="next_follow_up_at">Next follow-up</label>
                            <input id="next_follow_up_at" name="next_follow_up_at" type="date"
                                   class="form-control @error('next_follow_up_at') is-invalid @enderror"
                                   value="{{ old('next_follow_up_at', $lead->next_follow_up_at?->toDateString()) }}"
                                   min="{{ today()->toDateString() }}">
                        </div>
                        <button class="btn btn--primary btn--sm">Save note</button>
                    </div>
                    @error('next_follow_up_at')<span class="form-error">{{ $message }}</span>@enderror
                </form>
            </x-panel.box>

            <x-panel.box title="History">
                @if ($lead->notes->isEmpty())
                    <p class="text-muted-hp u-mb-0">No notes yet.</p>
                @else
                    <ol class="note-list">
                        @foreach ($lead->notes as $note)
                            <li class="note-list__item">
                                <div class="note-list__meta">
                                    <strong>{{ $note->user?->name ?? 'System' }}</strong>
                                    <time datetime="{{ $note->created_at->toIso8601String() }}">
                                        {{ $note->created_at->format('d M Y, h:i A') }}
                                    </time>
                                </div>

                                @if ($note->isStatusChange())
                                    <p class="note-list__status">
                                        {{ $note->status_from ? \App\Enums\LeadStatus::from($note->status_from)->label() : 'Created' }}
                                        &rarr;
                                        {{ \App\Enums\LeadStatus::from($note->status_to)->label() }}
                                    </p>
                                @endif

                                @if ($note->note)
                                    <p class="u-mb-0">{{ $note->note }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-panel.box>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Deal value only matters for a won deal.
    (function () {
        const status = document.getElementById('status');
        const group = document.getElementById('deal-value-group');
        const sync = () => { group.hidden = status.value !== 'closed_won'; };
        status.addEventListener('change', sync);
        sync();
    })();
</script>
@endpush
