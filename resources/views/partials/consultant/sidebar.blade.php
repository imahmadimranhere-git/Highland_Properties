@php
    $nav = [
        ['route' => 'consultant.dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
        ['route' => 'consultant.leads.index', 'label' => 'My Leads', 'icon' => 'users'],
        ['route' => 'consultant.projects.index', 'label' => 'Projects', 'icon' => 'building'],
        ['route' => 'consultant.reports.index', 'label' => 'My Reports', 'icon' => 'file'],
        ['route' => 'consultant.profile.edit', 'label' => 'My Profile', 'icon' => 'user'],
    ];
@endphp

<aside class="panel-sidebar" id="panel-sidebar">
    <a href="{{ route('consultant.dashboard') }}" class="panel-sidebar__brand">
        Highland <span>Sales</span>
    </a>

    <nav class="panel-nav" aria-label="Consultant">
        @foreach ($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="panel-nav__link {{ request()->routeIs($item['route']) ? 'is-active' : '' }}">
                <x-ui.icon :name="$item['icon']" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</aside>
