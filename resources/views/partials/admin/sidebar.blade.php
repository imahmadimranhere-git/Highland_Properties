@php
    // Route names are placeholders until each module is built in step 4.
    $nav = [
        'Overview' => [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
        ],
        'Inventory' => [
            ['route' => 'admin.projects.index', 'label' => 'Projects & Listings', 'icon' => 'building'],
            ['route' => 'admin.unit-categories.index', 'label' => 'Unit Categories', 'icon' => 'layers'],
            ['route' => 'admin.development-updates.index', 'label' => 'Development Updates', 'icon' => 'clock'],
            ['route' => 'admin.developers.index', 'label' => 'Developers', 'icon' => 'briefcase'],
            ['route' => 'admin.master-data.index', 'label' => 'Master Data', 'icon' => 'sliders'],
        ],
        'Sales' => [
            ['route' => 'admin.leads.index', 'label' => 'Lead Management', 'icon' => 'users'],
            ['route' => 'admin.reports.index', 'label' => 'Reports', 'icon' => 'file'],
        ],
        'People' => [
            ['route' => 'admin.users.index', 'label' => 'Users & Roles', 'icon' => 'shield'],
            ['route' => 'admin.team.index', 'label' => 'Team / Agents', 'icon' => 'user'],
        ],
        'Website' => [
            ['route' => 'admin.content.index', 'label' => 'Content', 'icon' => 'edit'],
            ['route' => 'admin.settings.index', 'label' => 'Website Settings', 'icon' => 'settings'],
            ['route' => 'admin.media.index', 'label' => 'Media Library', 'icon' => 'image'],
        ],
    ];
@endphp

<aside class="panel-sidebar" id="panel-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="panel-sidebar__brand">
        Highland <span>Admin</span>
    </a>

    <nav class="panel-nav" aria-label="Admin">
        @foreach ($nav as $group => $items)
            <div class="panel-nav__group">{{ $group }}</div>

            @foreach ($items as $item)
                <a href="{{ route($item['route']) }}"
                   class="panel-nav__link {{ request()->routeIs($item['route']) ? 'is-active' : '' }}">
                    <x-ui.icon :name="$item['icon']" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        @endforeach
    </nav>
</aside>
