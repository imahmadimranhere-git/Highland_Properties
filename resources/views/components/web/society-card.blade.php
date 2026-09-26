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

        @php $place = collect([$society->location?->name, $society->city?->name])->filter()->implode(', '); @endphp

        {{-- A picture of where it is, with the place name written on it.
             Falls back to the plain line when no image has been uploaded. --}}
        @if ($society->relationLoaded('locationImage') && $society->locationImage)
            <figure class="card__place">
                <img src="{{ $society->locationImage->thumb_url }}" alt="{{ $society->locationImage->alt_text ?: $place }}"
                     width="480" height="150" loading="lazy" decoding="async">
                <figcaption>
                    <x-ui.icon name="pin" :size="14" class="icon icon--gold" />
                    {{ $place }}
                </figcaption>
            </figure>
        @else
            <p class="card__meta">
                <x-ui.icon name="pin" :size="15" class="icon icon--gold" />
                {{ $place }}
            </p>
        @endif

        <div class="u-between">
            <div>
                @if ($society->starting_price)
                    <span class="card__price-label">Plots from</span>
                    <span class="card__price">{{ money($society->starting_price) }}</span>
                @endif
            </div>
        </div>

        <a href="{{ route('societies.show', $society->slug) }}" class="card__action">
            View details
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6"/>
            </svg>
        </a>
    </div>
</article>
