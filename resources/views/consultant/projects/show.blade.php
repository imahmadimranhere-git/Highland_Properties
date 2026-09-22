@extends('layouts.consultant')

@section('title', $project->name)

@section('content')
    <x-panel.page-head
        :title="$project->name"
        :sub="collect([$project->location?->name, $project->city?->name, $project->developer?->name])->filter()->implode(' · ')">
        <x-slot:actions>
            @if ($project->brochure_path)
                <a href="{{ Storage::disk('public')->url($project->brochure_path) }}" target="_blank" rel="noopener" class="btn btn--tertiary btn--sm">
                    <x-ui.icon name="download" :size="16" /> Brochure
                </a>
            @endif
        </x-slot:actions>
    </x-panel.page-head>

    {{-- Share ------------------------------------------------------------ --}}
    <x-panel.box title="Share with a client">
        <div class="u-flex u-gap-8 u-wrap" style="align-items:center;">
            <input id="share-url" type="text" readonly class="form-control" value="{{ $publicUrl }}" style="flex:1 1 280px;" aria-label="Project link">
            <button type="button" class="btn btn--secondary btn--sm" data-copy="#share-url">Copy link</button>
            <a class="btn btn--primary btn--sm" target="_blank" rel="noopener"
               href="https://wa.me/?text={{ rawurlencode($project->name . ' — ' . $publicUrl) }}">Send on WhatsApp</a>
        </div>
        @unless ($project->is_published)
            <p class="form-hint u-mb-0">This project is not live yet — the link will work once the admin publishes it.</p>
        @endunless
    </x-panel.box>

    {{-- Overview --------------------------------------------------------- --}}
    <div class="row">
        <div class="col-lg-7">
            <x-panel.box title="Overview">
                <dl class="detail-list">
                    <dt>Status</dt><dd><x-ui.status-badge :status="$project->status" /></dd>
                    <dt>Type</dt><dd>{{ $project->projectType?->name ?? '—' }}</dd>
                    <dt>Area</dt><dd>{{ $project->total_area ?: '—' }}</dd>
                    <dt>Floors / units</dt><dd>{{ $project->total_floors ?: '—' }} / {{ $project->total_units ?: '—' }}</dd>
                    <dt>Completion</dt><dd>{{ $project->completion_target?->format('F Y') ?? '—' }}</dd>
                    <dt>Approvals</dt><dd>{{ $project->approvals ?: '—' }}</dd>
                    <dt>Address</dt><dd>{{ $project->address ?: '—' }}</dd>
                </dl>

                @if ($project->description)
                    <hr class="rule-gold" style="margin:18px 0;">
                    <p class="u-mb-0" style="white-space:pre-line;">{{ $project->description }}</p>
                @endif
            </x-panel.box>
        </div>

        <div class="col-lg-5">
            <x-panel.box title="Amenities">
                @forelse ($project->amenities as $amenity)
                    <span class="badge badge-navy" style="margin:0 6px 8px 0;">{{ $amenity->name }}</span>
                @empty
                    <p class="text-muted-hp u-mb-0">None listed.</p>
                @endforelse
            </x-panel.box>

            @if ($project->nearby_landmarks)
                <x-panel.box title="Nearby">
                    <ul class="u-mb-0" style="padding-left:18px;">
                        @foreach ($project->nearby_landmarks as $landmark)
                            <li>{{ $landmark }}</li>
                        @endforeach
                    </ul>
                </x-panel.box>
            @endif
        </div>
    </div>

    {{-- Unit categories -------------------------------------------------- --}}
    <x-panel.box title="Unit categories" flush>
        @if ($project->unitCategories->isEmpty())
            <x-ui.empty-state title="No categories yet" />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Category</th><th>Unit type</th><th>Size</th><th>Availability</th><th>Total price</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($project->unitCategories as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->unit_type }}</td>
                                <td>{{ $category->size_label ?? '—' }}</td>
                                <td><x-ui.status-badge :status="$category->availability" /></td>
                                <td class="is-price">{{ money($category->total_price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel.box>

    {{-- Payment plans ---------------------------------------------------- --}}
    @if ($project->unitCategories->whereNotNull('paymentPlan')->isNotEmpty())
        <p class="section-label">Payment plans</p>
        <div class="u-grid u-mb-24">
            @foreach ($project->unitCategories as $category)
                @continue(! $category->paymentPlan)
                @php $plan = $category->paymentPlan; @endphp

                <div class="card card--featured">
                    <div class="card__body">
                        <h3 class="card__title">{{ $category->name }}</h3>
                        <p class="text-muted-hp" style="font-size:.875rem;">{{ $category->unit_type }}</p>

                        <dl class="detail-list" style="grid-template-columns:1fr auto;">
                            <dt>Booking</dt><dd class="is-price">{{ money($plan->booking_amount) }}</dd>
                            <dt>Down payment</dt><dd class="is-price">{{ money($plan->down_payment) }}</dd>
                            <dt>{{ $plan->installment_count }} × {{ strtolower($plan->installment_frequency->label()) }}</dt>
                            <dd class="is-price">{{ money($plan->installment_amount) }}</dd>
                            <dt>On possession</dt><dd class="is-price">{{ money($plan->possession_charges) }}</dd>
                        </dl>

                        @if ($plan->notes)
                            <p class="form-hint u-mb-0">{{ $plan->notes }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Development updates --------------------------------------------- --}}
    <x-panel.box title="Development updates">
        @if ($project->developmentUpdates->isEmpty())
            <p class="text-muted-hp u-mb-0">No site updates published yet.</p>
        @else
            <ol class="note-list">
                @foreach ($project->developmentUpdates as $update)
                    <li class="note-list__item">
                        <div class="note-list__meta">
                            <strong>{{ $update->title }}</strong>
                            <time datetime="{{ $update->update_date->toDateString() }}">{{ $update->update_date->format('d M Y') }}</time>
                        </div>
                        @if ($update->description)<p>{{ $update->description }}</p>@endif
                        @if ($update->photos->isNotEmpty())
                            <div class="media-grid">
                                @foreach ($update->photos as $photo)
                                    <figure class="media-grid__item">
                                        <a href="{{ $photo->url }}" target="_blank" rel="noopener">
                                            <img src="{{ $photo->thumb_url }}" alt="{{ $update->title }}" loading="lazy" width="140" height="100">
                                        </a>
                                    </figure>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </x-panel.box>
@endsection

@push('scripts')
<script>
    // Copy-to-clipboard for the share link; no clipboard library needed.
    document.querySelectorAll('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const input = document.querySelector(btn.dataset.copy);
            try {
                await navigator.clipboard.writeText(input.value);
                window.hpToast?.('Link copied', 'success');
            } catch {
                input.select();
                document.execCommand('copy');
            }
        });
    });
</script>
@endpush
