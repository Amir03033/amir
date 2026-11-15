// Ping experiment: measure the visitor's round trip once the section is on screen, and on demand.
import { measurePing } from './terminal';

export function initPing() {
    const box = document.querySelector('[data-ping]');
    if (box) {
        const value = box.querySelector('[data-ping-value]');
        const note = box.querySelector('[data-ping-note]');
        const again = box.querySelector('[data-ping-again]');
        const run = async () => {
            again.disabled = true;
            note.textContent = note.dataset.measuring;
            const ms = await measurePing(box.dataset.pingUrl, 5);
            value.textContent = ms === null ? '—' : String(ms);
            note.textContent = note.dataset.median.replace(':n', '5');
            again.disabled = false;
        };
        again.addEventListener('click', run);
        const io = new IntersectionObserver(([entry]) => { if (entry.isIntersecting) { io.disconnect(); run(); } }, { threshold: 0.3 });
        io.observe(box);
    }

    // ServerScope status badge follows the theme.
    const badges = document.querySelectorAll('img[data-badge]');
    const sync = () => badges.forEach((img) => { img.src = img.dataset.badge + document.documentElement.dataset.theme; });
    if (badges.length) { sync(); window.addEventListener('themechange', sync); }
}
