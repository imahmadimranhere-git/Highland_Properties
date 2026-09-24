@php
    // 'active' lists every route that should light up the item,
    // so opening a single lead still highlights "My Leads".
    $nav = [
        ['route' => 'consultant.dashboard', 'active' => ['consultant.dashboard'], 'label' => 'Dashboard', 'icon' => 'grid'],
        ['route' => 'consultant.leads.index', 'active' => ['consultant.leads.*'], 'label' => 'My Leads', 'icon' => 'users', 'badge' => 'leads'],
        ['route' => 'consultant.projects.index', 'active' => ['consultant.projects.*'], 'label' => 'Projects', 'icon' => 'building'],
        ['route' => 'consultant.reports.index', 'active' => ['consultant.reports.*'], 'label' => 'My Reports', 'icon' => 'file'],
        ['route' => 'consultant.profile.edit', 'active' => ['consultant.profile.*'], 'label' => 'My Profile', 'icon' => 'user'],
    ];
@endphp

<aside class="panel-sidebar" id="panel-sidebar">
    <a href="{{ route('consultant.dashboard') }}" class="panel-sidebar__brand">
        Highland <span>Sales</span>
    </a>

    <nav class="panel-nav" aria-label="Consultant">
        @foreach ($nav as $item)
            @php $count = isset($item['badge']) ? ($badges[$item['badge']] ?? 0) : 0; @endphp

            <a href="{{ route($item['route']) }}"
               class="panel-nav__link {{ request()->routeIs(...$item['active']) ? 'is-active' : '' }}">
                <x-ui.icon :name="$item['icon']" />
                <span>{{ $item['label'] }}</span>

                @if ($count > 0)
                    <span class="panel-nav__badge" aria-label="{{ $count }} needing attention">{{ $count > 99 ? '99+' : $count }}</span>
                @endif
            </a>
        @endforeach
    </nav>
</aside>
