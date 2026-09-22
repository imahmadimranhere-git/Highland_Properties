/**
 * Project detail page only: gallery lightbox and installment calculator.
 *
 * The lightbox is ~50 lines on the native <dialog> element instead of a
 * lightbox library, so the page ships no third-party JavaScript at all.
 */

function money(amount) {
    if (!Number.isFinite(amount)) return '—';
    if (amount >= 10000000) return 'PKR ' + (amount / 10000000).toFixed(2).replace(/\.?0+$/, '') + ' Crore';
    if (amount >= 100000) return 'PKR ' + (amount / 100000).toFixed(2).replace(/\.?0+$/, '') + ' Lac';
    return 'PKR ' + Math.round(amount).toLocaleString('en-PK');
}

/* Lightbox ------------------------------------------------------------ */
function initLightbox() {
    const groups = document.querySelectorAll('[data-lightbox-group]');
    if (!groups.length) return;

    const dialog = document.createElement('dialog');
    dialog.className = 'lightbox';
    dialog.setAttribute('aria-label', 'Image viewer');
    dialog.innerHTML = `
        <img class="lightbox__img" alt="">
        <button type="button" class="lightbox__btn lightbox__close" aria-label="Close">&times;</button>
        <button type="button" class="lightbox__btn lightbox__prev" aria-label="Previous image">&#8249;</button>
        <button type="button" class="lightbox__btn lightbox__next" aria-label="Next image">&#8250;</button>
        <p class="lightbox__count" aria-live="polite"></p>`;
    document.body.append(dialog);

    const img = dialog.querySelector('.lightbox__img');
    const count = dialog.querySelector('.lightbox__count');
    let items = [];
    let current = 0;

    const show = (i) => {
        current = (i + items.length) % items.length;
        const link = items[current];
        img.src = link.href;
        img.alt = link.querySelector('img')?.alt || '';
        count.textContent = `${current + 1} / ${items.length}`;
        dialog.querySelector('.lightbox__prev').hidden = items.length < 2;
        dialog.querySelector('.lightbox__next').hidden = items.length < 2;
    };

    groups.forEach((group) => {
        const links = Array.from(group.querySelectorAll('[data-lightbox]'));
        links.forEach((link, i) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                items = links;
                show(i);
                dialog.showModal();
            });
        });
    });

    dialog.querySelector('.lightbox__close').addEventListener('click', () => dialog.close());
    dialog.querySelector('.lightbox__prev').addEventListener('click', () => show(current - 1));
    dialog.querySelector('.lightbox__next').addEventListener('click', () => show(current + 1));
    dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });
    dialog.addEventListener('close', () => { img.removeAttribute('src'); });

    document.addEventListener('keydown', (e) => {
        if (!dialog.open) return;
        if (e.key === 'ArrowLeft') show(current - 1);
        if (e.key === 'ArrowRight') show(current + 1);
    });
}

/* Installment calculator ---------------------------------------------- */
function initCalculator() {
    const root = document.querySelector('[data-calculator]');
    if (!root) return;

    const select = root.querySelector('[data-calc-category]');
    const downInput = root.querySelector('[data-calc-down]');
    const out = (key) => root.querySelector(`[data-out="${key}"]`);

    const plan = () => {
        const o = select.selectedOptions[0].dataset;
        return {
            price: +o.price, booking: +o.booking, down: +o.down,
            count: +o.count, months: +o.months, possession: +o.possession,
            installment: +o.installment,
        };
    };

    const render = () => {
        const p = plan();
        const down = Math.max(0, Math.min(+downInput.value || 0, p.price - p.booking - p.possession));

        // With the published down payment, show the published installment
        // exactly. If the visitor changes it, spread whatever is not paid at
        // booking, as down payment or on possession evenly over the installments.
        const unchanged = down === p.down;
        const each = p.count > 0
            ? (unchanged ? p.installment : Math.max(0, p.price - p.booking - down - p.possession) / p.count)
            : 0;
        const remaining = each * p.count;
        const years = (p.count * p.months) / 12;

        out('booking').textContent = money(p.booking);
        out('down').textContent = money(down);
        out('label').textContent = p.count
            ? `${p.count} × ${p.months === 3 ? 'quarterly' : 'monthly'} (${years % 1 ? years.toFixed(1) : years} yrs)`
            : 'Installments';
        out('installment').textContent = p.count ? money(each) : '—';
        out('possession').textContent = money(p.possession);
        out('total').textContent = money(p.booking + down + remaining + p.possession);
    };

    select.addEventListener('change', () => {
        downInput.value = plan().down;
        render();
    });
    downInput.addEventListener('input', render);

    downInput.value = plan().down;
    render();
}

initLightbox();
initCalculator();
