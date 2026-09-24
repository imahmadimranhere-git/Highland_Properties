@php
    $nav = [
        'Overview' => [
            ['route' => 'admin.dashboard', 'active' => ['admin.dashboard'], 'label' => 'Dashboard', 'icon' => 'grid'],
        ],
        'Inventory' => [
            ['route' => 'admin.societies.index', 'active' => ['admin.societies.*'], 'label' => 'Societies', 'icon' => 'sliders'],
            ['route' => 'admin.projects.index', 'active' => ['admin.projects.index', 'admin.projects.create', 'admin.projects.edit'], 'label' => 'High-Rise Projects', 'icon' => 'building'],
            ['route' => 'admin.unit-categories.index', 'active' => ['admin.unit-categories.*', 'admin.projects.categories.*'], 'label' => 'Unit Categories', 'icon' => 'layers'],
            ['route' => 'admin.development-updates.index', 'active' => ['admin.development-updates.*', 'admin.projects.updates.*'], 'label' => 'Development Updates', 'icon' => 'clock'],
            ['route' => 'admin.developers.index', 'active' => ['admin.developers.*'], 'label' => 'Developers', 'icon' => 'briefcase'],
            ['route' => 'admin.master-data.index', 'active' => ['admin.master-data.*'], 'label' => 'Master Data', 'icon' => 'sliders'],
        ],
        'Sales' => [
            ['route' => 'admin.leads.index', 'active' => ['admin.leads.*'], 'label' => 'Lead Management', 'icon' => 'users'],
            ['route' => 'admin.messages.index', 'active' => ['admin.messages.*'], 'label' => 'Inbox', 'icon' => 'mail'],
            ['route' => 'admin.reports.index', 'active' => ['admin.reports.*'], 'label' => 'Reports', 'icon' => 'file'],
        ],
        'People' => [
            ['route' => 'admin.users.index', 'active' => ['admin.users.*'], 'label' => 'Users & Roles', 'icon' => 'shield'],
            ['route' => 'admin.team.index', 'active' => ['admin.team.*'], 'label' => 'Team / Agents', 'icon' => 'user'],
        ],
        'Website' => [
            ['route' => 'admin.content.index', 'active' => ['admin.content.*', 'admin.posts.*', 'admin.testimonials.*'], 'label' => 'Content', 'icon' => 'edit'],
            ['route' => 'admin.settings.index', 'active' => ['admin.settings.*'], 'label' => 'Website Settings', 'icon' => 'settings'],
            ['route' => 'admin.media.index', 'active' => ['admin.media.*'], 'label' => 'Media Library', 'icon' => 'image'],
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
                   class="panel-nav__link {{ request()->routeIs(...$item['active']) ? 'is-active' : '' }}">
                    <x-ui.icon :name="$item['icon']" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        @endforeach
    </nav>
</aside>
