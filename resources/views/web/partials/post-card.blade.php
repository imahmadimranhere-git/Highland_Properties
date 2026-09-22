<article class="card card--interactive">
    <a href="{{ route('blog.show', $post->slug) }}" class="card__media" tabindex="-1" aria-hidden="true" style="aspect-ratio:16/10;">
        @if ($post->cover)
            <img src="{{ $post->cover->thumb_url }}" alt="" width="480" height="300" loading="lazy" decoding="async">
        @else
            <span class="img-ph">{{ setting('site_name') }}</span>
        @endif
    </a>
    <div class="card__body">
        <p class="card__price-label">{{ $post->published_at?->format('d F Y') }}</p>
        <h3 class="project-card__name"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
        @if ($post->excerpt)<p class="text-muted-hp u-mb-0">{{ $post->excerpt }}</p>@endif
    </div>
</article>
