// Theme toggle: cycles system → light → dark. The pre-paint script in the layout owns window.__theme.
const order = ['system', 'light', 'dark'];

export function initTheme() {
    const theme = window.__theme;
    if (!theme) return;

    const next = () => order[(order.indexOf(theme.pref()) + 1) % order.length];
    const label = () => document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        const text = btn.dataset['label' + theme.pref().charAt(0).toUpperCase() + theme.pref().slice(1)];
        btn.setAttribute('title', text);
        const span = btn.querySelector('[data-theme-label]');
        if (span) span.textContent = text;
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('[data-theme-toggle]')) return;
        theme.set(next());
        label();
    });

    // LEGACY: the old ⌘K palette calls window.toggleTheme().
    window.toggleTheme = () => { theme.set(next()); label(); };
    label();
}
