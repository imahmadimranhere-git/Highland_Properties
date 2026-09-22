<figure class="card card--accent-left testimonial">
    <div class="card__body">
        <div class="testimonial__stars" aria-label="{{ $t->rating }} out of 5">
            @for ($i = 1; $i <= 5; $i++)
                <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"
                     fill="{{ $i <= $t->rating ? 'var(--gold-500)' : 'var(--border)' }}">
                    <path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3Z"/>
                </svg>
            @endfor
        </div>

        <blockquote class="testimonial__text">&ldquo;{{ $t->message }}&rdquo;</blockquote>

        <figcaption class="testimonial__who">
            @if ($t->photo)
                <img src="{{ Storage::disk('public')->url($t->photo) }}" alt="" width="44" height="44" loading="lazy">
            @endif
            <span>
                <strong>{{ $t->name }}</strong>
                @if ($t->designation)<span class="text-muted-hp">{{ $t->designation }}</span>@endif
            </span>
        </figcaption>
    </div>
</figure>
