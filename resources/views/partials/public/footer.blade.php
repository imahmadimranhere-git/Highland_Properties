@php
    $socials = [
        'facebook' => '<path d="M15 3h-2a4 4 0 0 0-4 4v3H7v4h2v7h4v-7h3l1-4h-4V7a1 1 0 0 1 1-1h2V3Z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/>',
        'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
        'youtube' => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3V9Z"/>',
    ];
@endphp

<footer class="site-footer">
    <div class="u-container">
        <div class="site-footer__grid">
            <div>
                <div class="site-footer__brand">{{ setting('site_name', 'Highland Properties') }}</div>
                <p>{{ setting('tagline', 'Premium living, thoughtfully delivered.') }}</p>

                {{-- Plain SVG links: no social widget scripts, no tracking. --}}
                <div class="social-links">
                    @foreach ($socials as $key => $path)
                        @if ($url = setting($key))
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($key) }}">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $path !!}</svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="{{ route('societies.index') }}">Societies</a></li>
                    <li><a href="{{ route('projects.index') }}">High-rise projects</a></li>
                    <li><a href="{{ route('developers.index') }}">Developers</a></li>
                    <li><a href="{{ route('team') }}">Our team</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                </ul>
            </div>

            <div>
                <h4>Company</h4>
                <ul>
                    <li><a href="{{ route('about') }}">About us</a></li>
                    <li><a href="{{ route('testimonials') }}">Testimonials</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4>Get in touch</h4>
                <ul>
                    @if (setting('phone'))<li><a href="tel:{{ setting('phone') }}">{{ setting('phone') }}</a></li>@endif
                    @if (setting('email'))<li><a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a></li>@endif
                    @if (setting('address'))
                        <li>@include('web.partials.office', ['compact' => true])</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="site-footer__bottom">
            <span>&copy; {{ date('Y') }} {{ setting('site_name', 'Highland Properties') }}. All rights reserved.</span>
            <span>Marketed and developed with care.</span>
        </div>
    </div>
</footer>
