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


/**
 * Scroll reveal.
 *
 * IntersectionObserver, not a scroll handler: the browser tells us when an
 * element crosses into view instead of us asking on every scroll frame, which
 * is what makes this free on a phone. No animation library is involved.
 *
 * Sticky elements are deliberately excluded — a transform on an element (or
 * on its ancestor) cancels position: sticky in its children.
 */
function initReveal() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const targets = document.querySelectorAll([
        '.section-head',
        '.u-grid > .card:not(.card--sticky)',
        '.u-grid > article',
        '.value-grid > .card',
        '.plan-grid > .plan-card',
        '.team-grid > .team-card',
        '.timeline__item',
        '.video-split__text',
        '.video-split__player',
        '.fact-grid',
        '.office-box',
        '.post-link',
        '.contact-strip .u-between',
    ].join(','));

    if (! targets.length || ! ('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) return;

            entry.target.classList.add('is-in');
            observer.unobserve(entry.target);
            setTimeout(() => entry.target.classList.add('is-done'), 700);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    targets.forEach((el, index) => {
        el.classList.add('reveal');

        // Cards in the same row arrive a beat apart.
        const stagger = index % 3;
        if (stagger) el.classList.add(`reveal--${stagger}`);

        observer.observe(el);
    });
}

/**
 * A thin gold line at the top: it grows while the page is loading and while
 * the visitor is moving to the next page. Deliberately not a full-screen
 * loader — that hides a page that is already readable and feels slower.
 */
function initLoadBar() {
    const bar = document.createElement('div');
    bar.className = 'load-bar';
    document.body.append(bar);

    let width = 0;
    let timer = null;

    const creep = () => {
        timer = setInterval(() => {
            width = Math.min(width + (90 - width) * 0.12, 90);
            bar.style.width = `${width}%`;
        }, 180);
    };

    const finish = () => {
        clearInterval(timer);
        bar.style.width = '100%';
        bar.classList.add('is-done');
        setTimeout(() => { bar.style.width = '0'; bar.classList.remove('is-done'); }, 450);
    };

    if (document.readyState !== 'complete') {
        creep();
        window.addEventListener('load', finish, { once: true });
    }

    // Same bar when leaving for another page on this site.
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (! link) return;

        const url = new URL(link.href, window.location.origin);
        const sameTab = ! link.target || link.target === '_self';

        if (url.origin === window.location.origin && sameTab && url.pathname !== window.location.pathname) {
            width = 0;
            bar.classList.remove('is-done');
            creep();
        }
    });
}

/** Images fade in once decoded rather than snapping in half-drawn. */
function initImageFade() {
    document.querySelectorAll('img[loading="lazy"]').forEach((img) => {
        if (img.complete) return;

        img.classList.add('is-loading');
        img.addEventListener('load', () => {
            img.classList.remove('is-loading');
            img.classList.add('is-ready');
        }, { once: true });

        // A broken image must not stay invisible.
        img.addEventListener('error', () => img.classList.remove('is-loading'), { once: true });
    });
}

initHeaderHeight();
initMobileNav();
initReveal();
initLoadBar();
initImageFade();
initLazyEmbeds();
initVideoFacade();
initHeroRotation();
initFlashToasts();
