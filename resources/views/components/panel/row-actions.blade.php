@props(['label' => 'Row actions'])

{{--
    A button that says "Actions"; the menu itself is a row of icons.
    <details> handles opening and closing, so it works with the keyboard and
    without JavaScript; panel.js only lifts the open panel out of the table's
    scrollbox so it cannot be clipped.
--}}
<details class="row-menu">
    <summary class="row-menu__button" aria-label="{{ $label }}">
        Actions
        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </summary>

    <div class="row-menu__panel">
        {{ $slot }}
    </div>
</details>
