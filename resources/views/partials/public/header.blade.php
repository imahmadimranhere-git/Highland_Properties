<a href="#main" class="visually-hidden">Skip to content</a>

<header class="site-header">
    <div class="u-container site-header__inner">
        <a href="{{ route('home') }}" class="site-brand">
            @if (setting('logo'))
                <img src="{{ asset('storage/' . setting('logo')) }}" alt="{{ setting('site_name') }}" width="150" height="42">
            @else
                Highland <span>Properties</span>
            @endif
        </a>

        <nav id="site-nav" class="site-nav" aria-label="Main">
            @php
                $links = [
                    'home' => 'Home',
                    'about' => 'About',
                    'projects.index' => 'Projects',
                    'developers.index' => 'Developers',
                    'team' => 'Team',
                    'blog.index' => 'Blog',
                    'faq' => 'FAQ',
                    'contact' => 'Contact',
                ];
            @endphp

            @foreach ($links as $route => $label)
                <a href="{{ route($route) }}"
                   class="site-nav__link {{ request()->routeIs($route) ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="site-header__cta">
            <a href="tel:{{ setting('phone') }}" class="btn btn--secondary btn--sm">{{ setting('phone', 'Call us') }}</a>

            <button type="button" class="nav-burger" aria-controls="site-nav" aria-expanded="false" aria-label="Menu">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                </svg>
            </button>
        </div>
    </div>
</header>
