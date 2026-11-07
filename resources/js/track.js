// Conversion beacons: explicit [data-track] links, plus every click to another site ("outbound", labelled by host).
// Uses sendBeacon so navigation is never delayed. No cookies, no personal data.
export function initTracking() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token || !navigator.sendBeacon) return;

    const send = (name, label) => {
        const data = new FormData();
        data.append('_token', token);
        data.append('name', name);
        data.append('locale', document.documentElement.lang);
        if (label) data.append('label', label);
        navigator.sendBeacon('/e', data);
    };

    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[href]');
        if (!a) return;

        if (a.dataset.track) {
            send(a.dataset.track, a.dataset.trackLabel);
            return;
        }

        const url = new URL(a.href, location.href);
        if (url.protocol.startsWith('http') && url.host !== location.host) {
            send('outbound', url.host.replace(/^www\./, ''));
        }
    }, { capture: true });
}
