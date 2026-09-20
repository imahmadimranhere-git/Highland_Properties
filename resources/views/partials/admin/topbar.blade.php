<header class="panel-topbar">
    <div class="u-flex u-gap-16" style="align-items:center;">
        <button type="button" class="panel-burger" aria-controls="panel-sidebar" aria-expanded="false" aria-label="Menu">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </button>

        <p class="panel-topbar__title">@yield('title', 'Dashboard')</p>
    </div>

    <div class="u-flex u-gap-8" style="align-items:center;">
        <a href="{{ route('home') }}" class="btn btn--secondary btn--sm" target="_blank" rel="noopener">View site</a>

        <span class="panel-user">
            <span class="panel-user__avatar">{{ \Illuminate\Support\Str::of(auth()->user()->name)->substr(0, 2)->upper() }}</span>
            {{ auth()->user()->name }}
        </span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-icon" aria-label="Sign out" title="Sign out">
                <x-ui.icon name="download" :size="16" style="transform:rotate(-90deg);" />
            </button>
        </form>
    </div>
</header>
