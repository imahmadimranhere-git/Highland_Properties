/**
 * Shared behaviour for the admin and consultant panels.
 * Deliberately small: sidebar drawer, confirmation dialog and toasts.
 * No UI framework is loaded here.
 */
import { toast, initFlashToasts } from './toast.js';

function initSidebar() {
    const sidebar = document.querySelector('.panel-sidebar');
    const burger = document.querySelector('.panel-burger');
    if (!sidebar || !burger) return;

    let backdrop = null;

    const close = () => {
        sidebar.classList.remove('is-open');
        burger.setAttribute('aria-expanded', 'false');
        backdrop?.remove();
        backdrop = null;
    };

    burger.addEventListener('click', () => {
        const open = sidebar.classList.toggle('is-open');
        burger.setAttribute('aria-expanded', String(open));

        if (open) {
            backdrop = document.createElement('div');
            backdrop.className = 'panel-backdrop';
            backdrop.addEventListener('click', close);
            document.body.append(backdrop);
        } else {
            close();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}

/**
 * Every destructive or sensitive action goes through one shared <dialog>.
 * Markup: <form method="POST" data-confirm="Delete this project?" data-confirm-label="Delete">
 * data-confirm-label is optional; the button reads "Delete" by default.
 */
function initConfirm() {
    const dialog = document.getElementById('confirm-dialog');
    if (!dialog) return;

    const textEl = dialog.querySelector('[data-confirm-text]');
    const okBtn = dialog.querySelector('[data-confirm-ok]');
    let pendingForm = null;

    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-confirm]');
        if (!form || form.dataset.confirmed === '1') return;

        e.preventDefault();
        pendingForm = form;
        textEl.textContent = form.dataset.confirm;

        const label = form.dataset.confirmLabel || 'Delete';
        okBtn.textContent = label;
        okBtn.className = 'btn btn--sm ' + (label === 'Delete' ? 'btn--danger' : 'btn--primary');

        dialog.showModal();
    });

    okBtn.addEventListener('click', () => {
        dialog.close();
        if (!pendingForm) return;
        pendingForm.dataset.confirmed = '1';
        pendingForm.submit();
    });

    dialog.querySelectorAll('[data-confirm-cancel]').forEach((btn) => {
        btn.addEventListener('click', () => {
            pendingForm = null;
            dialog.close();
        });
    });
}


/**
 * Row action menus.
 *
 * <details> does the opening and closing by itself. Two things still need
 * script: only one menu should be open at a time, and the open panel has to
 * escape the table's horizontal scrollbox — an element with overflow-x: auto
 * clips anything that sticks out of it, which would cut the menu in half.
 * On open the panel is switched to fixed positioning at the button's
 * coordinates, which puts it above the table instead of inside it.
 */
function initRowMenus() {
    const menus = document.querySelectorAll('.row-menu');
    if (! menus.length) return;

    const place = (menu) => {
        const panel = menu.querySelector('.row-menu__panel');
        const rect = menu.querySelector('.row-menu__button').getBoundingClientRect();
        if (! panel) return;

        panel.classList.add('is-floating');
        panel.style.top = `${rect.bottom + 6}px`;

        // Keep the strip on screen when the button sits near the right edge.
        const width = panel.offsetWidth || 160;
        panel.style.left = `${Math.max(12, Math.min(rect.right - width, window.innerWidth - width - 12))}px`;
    };

    const closeAll = (except = null) => {
        menus.forEach((menu) => {
            if (menu !== except) menu.removeAttribute('open');
        });
    };

    menus.forEach((menu) => {
        menu.addEventListener('toggle', () => {
            if (! menu.open) return;
            closeAll(menu);
            place(menu);
        });
    });

    document.addEventListener('click', (event) => {
        if (! event.target.closest('.row-menu')) closeAll();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAll();
    });

    /*
     * A fixed panel does not travel with the page, so it follows the button
     * while anything scrolls — the page or the table's own scrollbox — and is
     * only dismissed when the window is resized.
     */
    const reposition = () => {
        menus.forEach((menu) => {
            if (menu.open) place(menu);
        });
    };

    window.addEventListener('scroll', reposition, { passive: true });
    document.querySelectorAll('.table-wrap').forEach((box) => {
        box.addEventListener('scroll', reposition, { passive: true });
    });
    window.addEventListener('resize', () => closeAll());
}

export function initPanel() {
    initRowMenus();
    initSidebar();
    initConfirm();
    initFlashToasts();
    window.hpToast = toast;
}
