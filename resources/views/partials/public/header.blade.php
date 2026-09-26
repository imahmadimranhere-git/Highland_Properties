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
                // FAQ and Developers still have pages (linked from the footer
                // and from project pages); they are simply not in the main menu.
                $links = [
                    'home' => 'Home',
                    'societies.index' => 'Societies',
                    'projects.index' => 'High-Rise Projects',
                    'team' => 'Team',
                    'blog.index' => 'Blog',
                    'about' => 'About Us',
                    'contact' => 'Contact',
                ];
            @endphp

            @foreach ($links as $route => $label)
                <a href="{{ route($route) }}"
                   class="site-nav__link {{ request()->routeIs($route) ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach

            {{-- Shown inside the mobile menu only; on a laptop the button in
                 the header already covers it. --}}
            @auth
                <a href="{{ auth()->user()->isSuperAdmin() ? route('admin.dashboard') : route('consultant.dashboard') }}"
                   class="site-nav__link site-nav__link--auth">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="site-nav__link site-nav__link--auth">Login</a>
            @endauth
        </nav>

        <div class="site-header__cta">
            {{-- Staff sign-in. Signed in already, it points at the right panel. --}}
            @auth
                <a href="{{ auth()->user()->isSuperAdmin() ? route('admin.dashboard') : route('consultant.dashboard') }}"
                   class="btn btn--primary btn--sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn--primary btn--sm">
                    <x-ui.icon name="user" :size="15" style="color:currentColor;" />
                    Login
                </a>
            @endauth

            <button type="button" class="nav-burger" aria-controls="site-nav" aria-expanded="false" aria-label="Menu">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                </svg>
            </button>
        </div>
    </div>
</header>
