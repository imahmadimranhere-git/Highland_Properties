<footer class="site-footer">
    <div class="u-container">
        <div class="site-footer__grid">
            <div>
                <div class="site-footer__brand">{{ setting('site_name', 'Highland Properties') }}</div>
                <p>{{ setting('tagline', 'Premium living, thoughtfully delivered.') }}</p>
            </div>

            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="{{ route('projects.index') }}">Our projects</a></li>
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
                    <li><a href="tel:{{ setting('phone') }}">{{ setting('phone') }}</a></li>
                    <li><a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a></li>
                    <li>{{ setting('address') }}</li>
                </ul>
            </div>
        </div>

        <div class="site-footer__bottom">
            <span>&copy; {{ date('Y') }} {{ setting('site_name', 'Highland Properties') }}. All rights reserved.</span>
            <span>Marketed and developed with care.</span>
        </div>
    </div>
</footer>
