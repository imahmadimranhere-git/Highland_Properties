<x-panel.box flush>
    <x-slot:actions>
        <form method="GET" class="u-flex u-gap-8">
            <input type="hidden" name="tab" value="testimonials">
            <select name="status" class="form-select" style="width:150px;">
                <option value="">All</option>
                @foreach (\App\Enums\TestimonialStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button class="btn btn--secondary btn--sm">Filter</button>
        </form>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn--primary btn--sm">
            <x-ui.icon name="plus" :size="16" /> Add
        </a>
    </x-slot:actions>

    @if ($testimonials->isEmpty())
        <x-ui.empty-state title="No testimonials" />
    @else
        <div class="table-wrap">
            <table class="table-hp">
                <thead><tr><th>Client</th><th>Message</th><th>Rating</th><th>Project</th><th>Status</th><th class="is-actions">Actions</th></tr></thead>
                <tbody>
                    @foreach ($testimonials as $t)
                        <tr>
                            <td>
                                <strong>{{ $t->name }}</strong>
                                <span style="display:block;font-size:.8125rem;" class="text-muted-hp">{{ $t->designation }}</span>
                                @if ($t->hasVideo())
                                    <span class="badge badge-gold" style="margin-top:4px;">Video</span>
                                @endif
                            </td>
                            <td style="max-width:360px;">{{ \Illuminate\Support\Str::limit($t->message, 110) }}</td>
                            <td>{{ $t->rating }} / 5</td>
                            <td>{{ $t->project?->name ?? '—' }}</td>
                            <td><x-ui.status-badge :status="$t->status" /></td>
                            <td class="is-actions">
                                @if ($t->status !== \App\Enums\TestimonialStatus::Approved)
                                    <form method="POST" action="{{ route('admin.testimonials.moderate', $t) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn-icon" aria-label="Approve" title="Approve"><x-ui.icon name="check" :size="16" /></button>
                                    </form>
                                @endif

                                @if ($t->status !== \App\Enums\TestimonialStatus::Rejected)
                                    <form method="POST" action="{{ route('admin.testimonials.moderate', $t) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn-icon btn-icon--danger" aria-label="Reject" title="Reject">&times;</button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn-icon" aria-label="Edit"><x-ui.icon name="pencil" :size="16" /></a>
                                <x-ui.delete-form :action="route('admin.testimonials.destroy', $t)" :confirm="'Delete the testimonial from ' . $t->name . '?'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $testimonials->links() }}
    @endif
</x-panel.box>
