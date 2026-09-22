<section class="contact-strip">
    <div class="u-container u-between u-wrap">
        <div>
            <h3>{{ $heading ?? 'Speak to a consultant' }}</h3>
            <p>{{ $text ?? 'Site visits are usually arranged within the same week.' }}</p>
        </div>

        <div class="u-flex u-gap-8 u-wrap">
            @if (setting('phone'))
                <a href="tel:{{ setting('phone') }}" class="btn btn--on-dark">
                    <x-ui.icon name="phone" :size="16" style="color:currentColor;" /> Call {{ setting('phone') }}
                </a>
            @endif
            @if (setting('whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', setting('whatsapp')) }}{{ isset($whatsappText) ? '?text=' . rawurlencode($whatsappText) : '' }}"
                   target="_blank" rel="noopener" class="btn btn--tertiary">WhatsApp us</a>
            @endif
        </div>
    </div>
</section>
