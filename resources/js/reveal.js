// Entrances, once: fade + rise + slight scale (CSS in motion.css). One shared observer.
export function initReveal() {
    const root = document.documentElement;

    // Stagger index for children of a reveal group.
    document.querySelectorAll('[data-reveal-group]').forEach((group) => {
        group.querySelectorAll('[data-reveal]').forEach((el, i) => el.style.setProperty('--i', Math.min(i, 8)));
    });

    const targets = document.querySelectorAll('[data-reveal]');
    if (!root.classList.contains('js-motion') || !('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-in'));
        root.classList.add('motion-ready');
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-in');
            io.unobserve(entry.target);
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -5% 0px' });

    targets.forEach((el) => io.observe(el));
    root.classList.add('motion-ready');
}
