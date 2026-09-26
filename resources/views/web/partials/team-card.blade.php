<article class="team-card" id="member-{{ $member->id }}">
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
        @if ($member->whatsapp)<a href="https://wa.me/{{ preg_replace('/\D+/', '', $member->whatsapp) }}" target="_blank" rel="noopener" aria-label="WhatsApp {{ $member->name }}">WA</a>@endif
    </div>

    {{--
        Message form. It does not send an email: the message is saved straight
        into this person's own portal as a lead, so nothing depends on an inbox
        being watched, and the reply is tracked with the rest of their work.

        <details> opens and closes with no JavaScript at all.
    --}}
    @if (session('team_message_sent') == $member->id)
        <p class="team-card__sent" role="status">
            Thank you — your message has reached {{ \Illuminate\Support\Str::before($member->name, ' ') }}.
        </p>
    @else
        <details class="team-message" @if ($errors->any() && old('member_id') == $member->id) open @endif>
            <summary>Send a message</summary>

            <form method="POST" action="{{ route('team.message', $member) }}" novalidate>
                @csrf
                <input type="hidden" name="member_id" value="{{ $member->id }}">

                <div aria-hidden="true" style="position:absolute;left:-9999px;">
                    <label for="website-{{ $member->id }}">Website</label>
                    <input id="website-{{ $member->id }}" type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                @if ($errors->any() && old('member_id') == $member->id)
                    <div class="alert alert--danger">{{ $errors->first() }}</div>
                @endif

                <div class="form-group">
                    <label class="form-label" for="tm-name-{{ $member->id }}">Your name <span class="required">*</span></label>
                    <input id="tm-name-{{ $member->id }}" name="name" type="text" maxlength="150" required autocomplete="name"
                           class="form-control" value="{{ old('member_id') == $member->id ? old('name') : '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="tm-phone-{{ $member->id }}">Phone <span class="required">*</span></label>
                    <input id="tm-phone-{{ $member->id }}" name="phone" type="tel" maxlength="30" required autocomplete="tel"
                           class="form-control" value="{{ old('member_id') == $member->id ? old('phone') : '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="tm-email-{{ $member->id }}">Email</label>
                    <input id="tm-email-{{ $member->id }}" name="email" type="email" maxlength="150" autocomplete="email"
                           class="form-control" value="{{ old('member_id') == $member->id ? old('email') : '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="tm-msg-{{ $member->id }}">Message <span class="required">*</span></label>
                    <textarea id="tm-msg-{{ $member->id }}" name="message" rows="3" maxlength="2000" required
                              class="form-control">{{ old('member_id') == $member->id ? old('message') : '' }}</textarea>
                </div>

                <button type="submit" class="btn btn--primary btn--sm btn--block">Send</button>
            </form>
        </details>
    @endif
</article>
