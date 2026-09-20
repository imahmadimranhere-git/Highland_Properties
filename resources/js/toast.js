/**
 * Toast notifications, shared by both panels.
 * Reads flash messages rendered into the page by Blade and can also be
 * called from other scripts: window.hpToast('Saved', 'success').
 */
export function toast(message, variant = 'default', timeout = 4500) {
    const stack = document.querySelector('.toast-stack') || createStack();

    const el = document.createElement('div');
    el.className = 'toast' + (variant === 'default' ? '' : ` toast--${variant}`);
    el.setAttribute('role', 'status');

    const text = document.createElement('span');
    text.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'toast__close';
    close.setAttribute('aria-label', 'Dismiss');
    close.textContent = '\u00D7';
    close.addEventListener('click', () => dismiss(el));

    el.append(text, close);
    stack.append(el);

    if (timeout) {
        setTimeout(() => dismiss(el), timeout);
    }
}

function createStack() {
    const stack = document.createElement('div');
    stack.className = 'toast-stack';
    document.body.append(stack);
    return stack;
}

function dismiss(el) {
    el.classList.add('is-leaving');
    setTimeout(() => el.remove(), 200);
}

/** Picks up <div data-toast data-variant="success">message</div> from Blade. */
export function initFlashToasts() {
    document.querySelectorAll('[data-toast]').forEach((node) => {
        toast(node.textContent.trim(), node.dataset.variant || 'default');
        node.remove();
    });
}
