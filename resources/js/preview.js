// Project rows: a screenshot follows the cursor with a little eased lag (desktop, fine pointer only).
export function initPreview() {
    if (!matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    const rows = document.querySelectorAll('[data-preview]');
    if (!rows.length) return;

    const box = document.createElement('div');
    box.className = 'preview';
    box.setAttribute('aria-hidden', 'true');
    const img = document.createElement('img');
    img.alt = '';
    img.decoding = 'async';
    box.append(img);
    document.body.append(box);

    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let x = 0, y = 0, cx = 0, cy = 0, raf = 0;
    const move = () => {
        cx += (x - cx) * (reduced ? 1 : 0.2);
        cy += (y - cy) * (reduced ? 1 : 0.2);
        box.style.transform = `translate3d(${cx + 24}px, ${cy - 90}px, 0)`;
        raf = Math.abs(x - cx) + Math.abs(y - cy) > 0.5 ? requestAnimationFrame(move) : 0;
    };

    rows.forEach((row) => {
        row.addEventListener('pointerenter', (e) => {
            const src = document.documentElement.dataset.theme === 'dark' ? (row.dataset.previewDark || row.dataset.preview) : row.dataset.preview;
            if (img.getAttribute('src') !== src) img.src = src;
            x = cx = e.clientX; y = cy = e.clientY;
            move();
            box.setAttribute('data-on', '');
        });
        row.addEventListener('pointermove', (e) => { x = e.clientX; y = e.clientY; if (!raf) raf = requestAnimationFrame(move); });
        row.addEventListener('pointerleave', () => box.removeAttribute('data-on'));
    });
    window.addEventListener('scroll', () => box.removeAttribute('data-on'), { passive: true });
}
