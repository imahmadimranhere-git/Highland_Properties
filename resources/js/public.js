/**
 * Public site script — loaded on every public page, so it stays tiny.
 * No framework and no Bootstrap JS. Page-specific code (lightbox,
 * calculator) lives in project.js and loads on the project page only.
 */
import { initFlashToasts } from './toast.js';

/**
 * Publishes the header's real height as --sticky-top, used by anything that
 * has to sit directly below it.
 *
 * It must NOT write to --header-h: the header sizes itself from that variable,
 * so feeding a measurement back into it creates a loop where the header grows
 * a little on every pass and the page keeps stretching.
 */
function initHeaderHeight() {
    const header = document.querySelector('.site-header');
    if (!header) return;

    let last = null;

    const publish = () => {
        const height = Math.round(header.getBoundingClientRect().height);

        // Write only on a real change, so the observer cannot trigger itself.
        if (height === last || height === 0) return;

        last = height;
        document.documentElement.style.setProperty('--sticky-top', `${height}px`);
    };

    publish();

    if ('ResizeObserver' in window) {
        new ResizeObserver(publish).observe(header);
    } else {
        window.addEventListener('resize', publish);
    }

    // Logos and fonts finish loading after this runs; re-measure once they do.
    window.addEventListener('load', publish);
}

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
            frame.style.borderRadius = '14px';
            frame.title = box.dataset.mapTitle || 'Location map';
            frame.referrerPolicy = 'no-referrer-when-downgrade';
            frame.allowFullscreen = true;
            box.replaceWith(frame);
        }, { once: true });
    });
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

/**
 * Cross-fades home banners and keeps the dots in step. Tapping a dot jumps
 * to that banner and stops the timer, so a visitor is never pulled away
 * from the one they chose.
 */
function initHeroRotation() {
    const hero = document.querySelector('[data-hero-rotate]');
    if (!hero) return;

    const slides = Array.from(hero.querySelectorAll('.hero__slide'));
    const dots = Array.from(hero.querySelectorAll('.hero-dots__dot'));
    if (slides.length < 2) return;

    let index = 0;
    let timer = null;

    const show = (next) => {
        slides[index].classList.remove('is-active');
        slides[index].setAttribute('aria-hidden', 'true');
        dots[index]?.classList.remove('is-active');
        dots[index]?.setAttribute('aria-selected', 'false');

        index = (next + slides.length) % slides.length;

        slides[index].classList.add('is-active');
        slides[index].setAttribute('aria-hidden', 'false');
        dots[index]?.classList.add('is-active');
        dots[index]?.setAttribute('aria-selected', 'true');
    };

    const start = () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        timer = setInterval(() => {
            if (!document.hidden) show(index + 1);
        }, 6500);
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            clearInterval(timer);
            show(Number(dot.dataset.slide));
        });
    });

    start();
}

initHeaderHeight();
initMobileNav();
initLazyEmbeds();
initVideoFacade();
initHeroRotation();
initFlashToasts();
