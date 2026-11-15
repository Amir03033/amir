// Live local time for <time data-clock="Europe/Amsterdam">. The server renders the right value first.
export function initClock() {
    const clocks = document.querySelectorAll('[data-clock]');
    if (!clocks.length) return;
    const fmt = (tz) => new Intl.DateTimeFormat('nl-NL', { timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: false });
    const tick = () => clocks.forEach((el) => { el.textContent = fmt(el.dataset.clock).format(new Date()); });
    const msToMinute = 60000 - (Date.now() % 60000);
    setTimeout(() => { tick(); setInterval(tick, 60000); }, msToMinute + 50);
}
