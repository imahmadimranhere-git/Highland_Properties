<nav class="tab-bar" aria-label="Report sections">
    <a href="{{ route('admin.reports.index') }}"
       class="tab-bar__link {{ request()->routeIs('admin.reports.index') ? 'is-active' : '' }}">Submitted reports</a>
    <a href="{{ route('admin.reports.summary') }}"
       class="tab-bar__link {{ request()->routeIs('admin.reports.summary') ? 'is-active' : '' }}">Automatic reports</a>
</nav>
