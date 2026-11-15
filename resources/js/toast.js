// window.toast('Bericht verstuurd', { type: 'success' | 'error' | 'info', timeout: 4000 })
const MAX = 3;

export function initToasts() {
    const region = document.querySelector('[data-toasts]');
    const icons = document.querySelector('[data-toast-icons]');
    if (!region) return;

    const iconFor = (type) => icons?.content.querySelector(`[data-type="${type}"]`)?.innerHTML ?? '';

    const remove = (el) => {
        if (el.dataset.leaving !== undefined) return;
        el.dataset.leaving = '';
        const done = () => el.remove();
        el.addEventListener('animationend', done, { once: true });
        setTimeout(done, 400);
    };

    window.toast = (message, { type = 'success', timeout = 4000 } = {}) => {
        while (region.children.length >= MAX) remove(region.firstElementChild);

        const el = document.createElement('div');
        el.className = 'toast';
        el.setAttribute('role', type === 'error' ? 'alert' : 'status');
        el.innerHTML = `${iconFor(type)}<p></p><button type="button" class="toast-close" aria-label="${region.dataset.labelClose}">${iconFor('close')}</button>`;
        el.querySelector('p').textContent = message;
        el.querySelector('.toast-close').addEventListener('click', () => remove(el));
        region.append(el);

        // Pause the timer while hovered or focused.
        let left = timeout, started = Date.now(), timer;
        const start = () => { started = Date.now(); timer = setTimeout(() => remove(el), left); };
        const pause = () => { clearTimeout(timer); left -= Date.now() - started; };
        el.addEventListener('pointerenter', pause);
        el.addEventListener('pointerleave', start);
        el.addEventListener('focusin', pause);
        el.addEventListener('focusout', start);
        start();
        return el;
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && region.lastElementChild && !document.querySelector('dialog[open]')) remove(region.lastElementChild);
    });

    if (region.dataset.flash) {
        try {
            const flash = JSON.parse(region.dataset.flash);
            window.toast(flash.message ?? flash, { type: flash.type ?? 'success' });
        } catch (e) { /* ignore malformed flash */ }
    }
}
