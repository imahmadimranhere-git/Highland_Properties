@props(['project', 'eager' => false])

{{--
    Project grid card. Uses the 480px thumbnail, never the full image.
    Pass :eager="true" only for cards visible without scrolling.
--}}
<article class="card card--interactive">
    <a href="{{ route('projects.show', $project->slug) }}" class="card__media" tabindex="-1" aria-hidden="true">
        <x-ui.status-badge :status="$project->status" class="badge--on-image" />

        @if ($project->cover)
            <x-web.picture
                :desktop="$project->coverFor('desktop')"
                :tablet="$project->coverFor('tablet')"
                :mobile="$project->coverFor('mobile')"
                :alt="$project->name"
                :width="480" :height="360"
                thumb
                :eager="$eager" />
        @else
            <span class="img-ph">Image coming soon</span>
        @endif
    </a>

    <div class="card__body">
        <h3 class="project-card__name">
            <a href="{{ route('projects.show', $project->slug) }}">{{ $project->name }}</a>
        </h3>

        @php $place = collect([$project->location?->name, $project->city?->name])->filter()->implode(', '); @endphp

        {{-- A picture of where it is, with the place name written on it.
             Falls back to the plain line when no image has been uploaded. --}}
        @if ($project->relationLoaded('locationImage') && $project->locationImage)
            <figure class="card__place">
                <img src="{{ $project->locationImage->thumb_url }}" alt="{{ $project->locationImage->alt_text ?: $place }}"
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

        @if ($project->starting_price)
            <span class="card__price-label">Starting from</span>
            <span class="card__price u-mb-16">{{ money($project->starting_price) }}</span>
        @endif

        <a href="{{ route('projects.show', $project->slug) }}" class="card__action">
            View details
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6"/>
            </svg>
        </a>
    </div>
</article>
