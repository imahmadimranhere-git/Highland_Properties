/**
 * Public site script. No framework and no Bootstrap JS:
 * the mobile menu and the lazy map are a few lines of plain JS each.
 */
import { initFlashToasts } from './toast.js';

function initMobileNav() {
    const nav = document.getElementById('site-nav');
    const burger = document.querySelector('.nav-burger');
    if (!nav || !burger) return;

    burger.addEventListener('click', () => {
        const open = nav.classList.toggle('is-open');
        burger.setAttribute('aria-expanded', String(open));
    });
}

/**
 * Maps and other iframes are only inserted after the visitor asks for them.
 * A Google Maps embed costs roughly 700KB and several requests, so it must
 * never load as part of the initial page.
 * Markup: <div class="map-placeholder" data-map-src="..."><button>Load map</button></div>
 */
function initLazyEmbeds() {
    document.querySelectorAll('[data-map-src]').forEach((box) => {
        box.addEventListener('click', () => {
            const frame = document.createElement('iframe');
            frame.src = box.dataset.mapSrc;
            frame.loading = 'lazy';
            frame.width = '100%';
            frame.height = '380';
            frame.style.border = '0';
            frame.title = box.dataset.mapTitle || 'Location map';
            frame.allowFullscreen = true;
            box.replaceWith(frame);
        }, { once: true });
    });
}

initMobileNav();
initLazyEmbeds();
initFlashToasts();
