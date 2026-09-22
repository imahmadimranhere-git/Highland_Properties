/**
 * Public site script — loaded on every public page, so it stays tiny.
 * No framework and no Bootstrap JS. Page-specific code (lightbox,
 * calculator) lives in project.js and loads on the project page only.
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
 * Maps are inserted only when the visitor asks for them. A Google Maps embed
 * is roughly 700KB and a dozen requests, so it never loads with the page.
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
            frame.style.borderRadius = '4px';
            frame.title = box.dataset.mapTitle || 'Location map';
            frame.referrerPolicy = 'no-referrer-when-downgrade';
            frame.allowFullscreen = true;
            box.replaceWith(frame);
        }, { once: true });
    });
}

/** Cross-fades home slides. Does nothing with a single slide or reduced motion. */
function initHeroRotation() {
    const hero = document.querySelector('[data-hero-rotate]');
    if (!hero || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const slides = Array.from(hero.querySelectorAll('.hero__slide'));
    let index = 0;

    setInterval(() => {
        if (document.hidden) return;

        slides[index].classList.remove('is-active');
        slides[index].setAttribute('aria-hidden', 'true');
        index = (index + 1) % slides.length;
        slides[index].classList.add('is-active');
        slides[index].setAttribute('aria-hidden', 'false');
    }, 6500);
}

initMobileNav();
initLazyEmbeds();
initHeroRotation();
initFlashToasts();
