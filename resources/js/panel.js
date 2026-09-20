/**
 * Shared behaviour for the admin and consultant panels.
 * Deliberately small: sidebar drawer, delete confirmation and toasts.
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
 * Every destructive action goes through one shared <dialog>.
 * Markup: <form method="POST" data-confirm="Delete this project?">
 */
function initDeleteConfirm() {
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

export function initPanel() {
    initSidebar();
    initDeleteConfirm();
    initFlashToasts();
    window.hpToast = toast;
}
