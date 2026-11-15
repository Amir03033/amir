// Scroll-scrubbed animation fallback. Browsers with CSS scroll-driven animations (Chromium, Safari 26+)
// run everything in motion.css off the main thread; this only runs elsewhere (e.g. Firefox today).
// One passive scroll listener, one rAF per frame, transform/opacity only, visible elements only.

const easeOut = (t) => 1 - Math.pow(1 - t, 3);
const clamp = (v) => Math.min(1, Math.max(0, v));

// Mirrors the @keyframes in motion.css.
const effects = {
    'fade-up': (p, e) => ({ opacity: e(p), transform: `translateY(${40 * (1 - e(p))}px)` }),
    'scale-in': (p, e) => ({ opacity: e(p), transform: `scale(${0.92 + 0.08 * e(p)})` }),
    'fade-out': (p) => ({ opacity: 1 - p, transform: `scale(${1 - 0.02 * p})` }),
    'hero-exit': (p) => ({ opacity: 1 - 0.8 * p, transform: `scale(${1 - 0.04 * p})` }),
    'dim-in': (p, e) => ({ opacity: 0.15 + 0.85 * e(p) }),
    'zoom-settle': (p) => ({ transform: `scale(${1.06 - 0.06 * p})` }),
    parallax: (p) => ({ transform: `translateY(${20 - 40 * p}px)` }),
};
const linear = new Set(['fade-out', 'hero-exit', 'zoom-settle', 'parallax']);

export function initScrub() {
    if (CSS.supports?.('animation-timeline: view()')) return;
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const pins = [...document.querySelectorAll('.pin')].map((pin) => ({
        el: pin,
        items: [...pin.querySelectorAll('[data-scrub]')].map((el) => ({
            el, fx: el.dataset.scrub, from: +el.dataset.from / 100, to: +el.dataset.to / 100,
        })),
    }));
    const views = [...document.querySelectorAll('[data-scrub-view]')].map((el) => ({
        el, fx: el.dataset.scrubView, anchor: el.dataset.scrubView === 'hero-exit' ? el.closest('[data-hero]') || el : el,
    }));
    if (!pins.length && !views.length) return;

    const apply = (el, fx, p) => {
        const style = effects[fx]?.(p, linear.has(fx) ? (t) => t : easeOut);
        if (!style) return;
        if ('opacity' in style) el.style.opacity = style.opacity.toFixed(3);
        if ('transform' in style) el.style.transform = style.transform;
    };

    // Approximations of the CSS view-timeline ranges used in motion.css.
    const viewProgress = (fx, rect, vh) => {
        switch (fx) {
            case 'hero-exit': return clamp(-rect.top / (rect.height * 0.6));
            case 'dim-in': return clamp((vh * 0.6 - rect.top) / (vh * 0.45));
            case 'zoom-settle': return clamp((vh - rect.top) / ((vh + rect.height) * 0.6));
            case 'parallax': return clamp((vh - rect.top) / (vh + rect.height));
            default: return clamp((vh - rect.top) / (vh * 0.4 + rect.height * 0.4));
        }
    };

    let ticking = false;
    const frame = () => {
        const vh = window.innerHeight;
        pins.forEach(({ el, items }) => {
            const rect = el.getBoundingClientRect();
            if (rect.bottom < -vh || rect.top > vh * 2) return;
            const p = clamp(-rect.top / Math.max(1, rect.height - vh));
            items.forEach((it) => apply(it.el, it.fx, clamp((p - it.from) / Math.max(0.0001, it.to - it.from))));
        });
        views.forEach(({ el, fx, anchor }) => {
            const rect = anchor.getBoundingClientRect();
            if (rect.bottom < -vh || rect.top > vh * 2) return;
            apply(el, fx, viewProgress(fx, rect, vh));
        });
        ticking = false;
    };

    const request = () => { if (!ticking) { ticking = true; requestAnimationFrame(frame); } };
    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request, { passive: true });
    frame();
}
