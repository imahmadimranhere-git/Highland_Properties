@props(['society', 'eager' => false])

<article class="card card--interactive">
    <a href="{{ route('societies.show', $society->slug) }}" class="card__media" tabindex="-1" aria-hidden="true">
        <x-ui.status-badge :status="$society->status" class="badge--on-image" />

        @if ($society->coverFor('desktop'))
            <x-web.picture
                :desktop="$society->coverFor('desktop')"
                :tablet="$society->coverFor('tablet')"
                :mobile="$society->coverFor('mobile')"
                :alt="$society->name"
                :width="480" :height="360"
                thumb
                :eager="$eager" />
        @else
            <span class="img-ph">Image coming soon</span>
        @endif
    </a>

    <div class="card__body">
        <h3 class="project-card__name">
            <a href="{{ route('societies.show', $society->slug) }}">{{ $society->name }}</a>
        </h3>

        <p class="card__meta">
            <x-ui.icon name="pin" :size="15" class="icon icon--gold" />
            {{ collect([$society->location?->name, $society->city?->name])->filter()->implode(', ') }}
        </p>

        <div class="u-between">
            <div>
                @if ($society->starting_price)
                    <span class="card__price-label">Plots from</span>
                    <span class="card__price">{{ money($society->starting_price) }}</span>
                @endif
            </div>
            @if ($society->total_plots)
                <span class="text-muted-hp" style="font-size:.8125rem;">{{ number_format($society->total_plots) }} plots</span>
            @endif
        </div>
    </div>
</article>
