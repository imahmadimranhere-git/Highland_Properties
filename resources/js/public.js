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
        box.addEventListener('click', (event) => {
            // The channel badge sits inside the player frame; clicking it must
            // open the channel, not start the video.
            if (event.target.closest('.video-embed__channel')) return;

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

/**
 * Swaps the video poster for the real YouTube player on click. Until then the
 * page loads no YouTube scripts at all — the embed is ~700KB.
 */
function initVideoFacade() {
    document.querySelectorAll('[data-video]').forEach((box) => {
        box.addEventListener('click', (event) => {
            // The channel badge sits inside the player frame; clicking it must
            // open the channel, not start the video.
            if (event.target.closest('.video-embed__channel')) return;

            const frame = document.createElement('iframe');
            frame.src = box.dataset.video;                 // already carries autoplay=1
            frame.title = 'Video';
            frame.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            frame.referrerPolicy = 'strict-origin-when-cross-origin';
            frame.allowFullscreen = true;

            box.querySelector('.video-embed__poster')?.remove();
            box.querySelector('.video-embed__play')?.remove();
            box.classList.add('is-playing');
            box.prepend(frame);
        });
    });
}

initMobileNav();
initVideoFacade();
initLazyEmbeds();
initHeroRotation();
initFlashToasts();
