<article class="team-card">
    <div class="team-card__photo">
        @if ($member->photo)
            <img src="{{ Storage::disk('public')->url($member->photo) }}" alt="{{ $member->name }}" width="480" height="480" loading="lazy" decoding="async">
        @else
            <span class="img-ph">{{ \Illuminate\Support\Str::of($member->name)->substr(0, 2)->upper() }}</span>
        @endif
    </div>
    <h3 class="team-card__name">{{ $member->name }}</h3>
    @if ($member->designation)<p class="team-card__role">{{ $member->designation }}</p>@endif
    @if (!empty($showBio) && $member->bio)<p class="text-muted-hp">{{ $member->bio }}</p>@endif

    <div class="team-card__links">
        @if ($member->phone)<a href="tel:{{ $member->phone }}" aria-label="Call {{ $member->name }}"><x-ui.icon name="phone" :size="16" /></a>@endif
        @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}"><x-ui.icon name="mail" :size="16" /></a>@endif
        @if ($member->whatsapp)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $member->whatsapp) }}" target="_blank" rel="noopener" aria-label="WhatsApp {{ $member->name }}">WA</a>@endif
    </div>
</article>
