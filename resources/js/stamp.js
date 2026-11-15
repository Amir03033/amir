// The hero stamp: leans toward the cursor (±10°, eased, no spring) and leaves a faint imprint on click/tap.
const MAX_TILT = 10;
const MAX_IMPRINTS = 4;

export function initStamp() {
    const area = document.querySelector('[data-stamp-area]');
    const stamp = area?.querySelector('[data-stamp]');
    const layer = area?.querySelector('[data-imprints]');
    if (!area || !stamp || !layer) return;

    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;

    // Only animate while the hero is on screen.
    new IntersectionObserver(([entry]) => area.toggleAttribute('data-visible', entry.isIntersecting)).observe(area);

    if (fine && !reduced) {
        let target = 0, current = 0, raf = 0;
        const step = () => {
            current += (target - current) * 0.12;
            stamp.style.setProperty('--tilt', `${current.toFixed(2)}deg`);
            raf = Math.abs(target - current) > 0.05 ? requestAnimationFrame(step) : 0;
        };
        area.addEventListener('pointermove', (e) => {
            const r = stamp.getBoundingClientRect();
            const dx = (e.clientX - (r.left + r.width / 2)) / (area.clientWidth / 2);
            target = Math.max(-1, Math.min(1, dx)) * MAX_TILT;
            if (!raf) raf = requestAnimationFrame(step);
        }, { passive: true });
        area.addEventListener('pointerleave', () => { target = 0; if (!raf) raf = requestAnimationFrame(step); });
    }

    const svg = stamp.querySelector('svg');
    const imprint = (x, y) => {
        while (layer.children.length >= MAX_IMPRINTS) layer.firstElementChild.remove();
        const el = document.createElement('div');
        el.className = 'imprint';
        el.style.left = `${x}px`;
        el.style.top = `${y}px`;
        el.style.setProperty('--r', `${Math.round(Math.random() * 60 - 30)}deg`);
        const copy = svg.cloneNode(true);
        copy.querySelector('defs')?.remove(); // keep ids unique; the ring text references the original path
        el.append(copy);
        el.addEventListener('animationend', () => el.remove(), { once: true });
        layer.append(el);
    };

    area.addEventListener('click', (e) => {
        // Links, buttons and text selection keep working normally; the stamp button itself stamps in the centre.
        if (e.target.closest('a, input, textarea, select') || String(window.getSelection())) return;
        const box = area.getBoundingClientRect();
        if (e.target.closest('[data-stamp]') && e.detail === 0) {
            imprint(box.width / 2, box.height * 0.45); // keyboard activation
            return;
        }
        imprint(e.clientX - box.left, e.clientY - box.top);
    });
}
