@props(['project', 'eager' => false])

{{--
    Project grid card. Uses the 480px thumbnail, never the full image.
    Pass :eager="true" only for cards visible without scrolling.
--}}
<article class="card card--interactive">
    <a href="{{ route('projects.show', $project->slug) }}" class="card__media" tabindex="-1" aria-hidden="true">
        <x-ui.status-badge :status="$project->status" class="badge--on-image" />

        @if ($project->cover)
            <img src="{{ $project->cover->thumb_url }}" alt="{{ $project->name }}"
                 width="480" height="360"
                 loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async">
        @else
            <span class="img-ph">Image coming soon</span>
        @endif
    </a>

    <div class="card__body">
        <h3 class="project-card__name">
            <a href="{{ route('projects.show', $project->slug) }}">{{ $project->name }}</a>
        </h3>

        <p class="card__meta">
            <x-ui.icon name="pin" :size="15" class="icon icon--gold" />
            {{ collect([$project->location?->name, $project->city?->name])->filter()->implode(', ') }}
        </p>

        @if ($project->starting_price)
            <span class="card__price-label">Starting from</span>
            <span class="card__price">{{ money($project->starting_price) }}</span>
        @endif
    </div>
</article>
