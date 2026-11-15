// Nav: quiet bar once scrolled, hides on scroll down and returns on scroll up; full-screen menu on mobile.
export function initNav() {
    const nav = document.querySelector('[data-nav]');
    const sheet = document.getElementById('nav-sheet');
    if (!nav) return;

    let lastY = window.scrollY;
    let ticking = false;

    const update = () => {
        const y = window.scrollY;
        nav.toggleAttribute('data-scrolled', y > 24);
        if (Math.abs(y - lastY) > 6) {
            nav.toggleAttribute('data-hidden', y > lastY && y > 400 && !sheet?.open);
            lastY = y;
        }
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, { passive: true });
    update();

    // Mobile sheet (a <dialog>: focus trap and Esc come with it).
    document.querySelectorAll('[data-nav-open]').forEach((btn) => btn.addEventListener('click', () => {
        sheet?.showModal();
        btn.setAttribute('aria-expanded', 'true');
    }));
    sheet?.addEventListener('close', () => document.querySelectorAll('[data-nav-open]').forEach((b) => b.setAttribute('aria-expanded', 'false')));
    sheet?.querySelectorAll('[data-nav-close]').forEach((el) => el.addEventListener('click', () => sheet.close()));
    // Closing on a breakpoint change avoids a stuck sheet when rotating a tablet.
    matchMedia('(min-width: 768px)').addEventListener('change', (e) => { if (e.matches && sheet?.open) sheet.close(); });

    // Active section on the home page: the link whose section crosses the middle of the viewport.
    const links = [...nav.querySelectorAll('[data-nav-link]')];
    const sections = links.map((a) => document.getElementById(a.dataset.navLink)).filter(Boolean);
    if (!sections.length) return;

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            links.forEach((a) => (a.dataset.navLink === entry.target.id
                ? a.setAttribute('aria-current', 'location')
                : a.removeAttribute('aria-current')));
        });
    }, { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach((s) => io.observe(s));
}
