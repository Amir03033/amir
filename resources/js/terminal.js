// The terminal toy. Works for any [data-terminal] (inline on home, in a dialog elsewhere).
// ⌘K / Ctrl K focuses the inline terminal or opens the dialog. Output is an aria-live log.

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const fill = (s, vars = {}) => Object.entries(vars).reduce((out, [k, v]) => out.replaceAll(`:${k}`, v), s);

export async function measurePing(url, n = 5) {
    const times = [];
    for (let i = 0; i < n; i++) {
        const t0 = performance.now();
        try { await fetch(`${url}?t=${Date.now()}-${i}`, { cache: 'no-store' }); } catch (e) { return null; }
        times.push(performance.now() - t0);
    }
    times.sort((a, b) => a - b);
    return Math.round(times[Math.floor(times.length / 2)]);
}

function setup(root) {
    const cfg = JSON.parse(root.dataset.config);
    const t = cfg.t;
    const out = root.querySelector('[data-term-out]');
    const form = root.querySelector('[data-term-form]');
    const input = root.querySelector('[data-term-input]');
    const history = [];
    let cursor = 0;

    const line = (text, cls = '') => {
        const p = document.createElement('p');
        if (cls) p.className = cls;
        p.textContent = text;
        out.append(p);
        out.scrollTop = out.scrollHeight;
        return p;
    };
    const go = (url, external = false) => {
        if (!url) return;
        setTimeout(() => (external ? window.open(url, '_blank', 'noopener') : (window.location.href = url)), 300);
    };
    const post = async (url, body) => {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify({ ...body, website: '', locale: document.documentElement.lang }),
        });
        return r.ok;
    };

    const commands = {
        help: () => Object.entries(t.help).forEach(([cmd, desc]) => line(`${cmd.padEnd(26)}${desc}`)),
        about: () => line(cfg.about),
        whoami: () => line('guest@amirs.life'),
        projects: () => cfg.projects.forEach((p, i) => line(`[${i + 1}] ${p.title}${p.line ? ' — ' + p.line : ''}`)),
        open: ([n]) => {
            const p = cfg.projects[parseInt(n, 10) - 1];
            if (!p) return line(t.usage_open);
            line(fill(t.opening, { what: p.title }), 'term-muted');
            go(p.url);
        },
        now: () => line(cfg.now || t.now_empty),
        contact: () => { line(cfg.email); if (cfg.links.linkedin) line(cfg.links.linkedin); },
        mail: () => go(`mailto:${cfg.email}`),
        ask: async (args) => {
            const q = args.join(' ').trim();
            if (q.length < 10) return line(t.usage_ask);
            line(t.sending, 'term-muted');
            line((await post(cfg.endpoints.ask, { body: q }).catch(() => false)) ? t.sent_question : t.failed);
        },
        idea: async (args) => {
            const title = args.join(' ').trim();
            if (title.length < 4) return line(t.usage_idea);
            line(t.sending, 'term-muted');
            line((await post(cfg.endpoints.idea, { title }).catch(() => false)) ? t.sent_idea : t.failed);
        },
        ping: async () => {
            line(t.pinging, 'term-muted');
            const ms = await measurePing(cfg.endpoints.ping);
            line(ms === null ? t.failed : fill(t.ping_result, { ms, n: 5 }));
        },
        theme: ([mode]) => {
            if (!['light', 'dark', 'system'].includes(mode)) return line(t.usage_theme);
            window.__theme?.set(mode);
            line(fill(t.theme_set, { theme: mode }));
        },
        lang: ([l]) => { if (cfg.links[l]) { line(fill(t.opening, { what: l })); go(cfg.links[l]); } },
        jebbari: () => { line(fill(t.opening, { what: 'jebbari.nl' })); go(cfg.links.jebbari, true); },
        serverscope: () => { if (cfg.links.serverscope) { line(fill(t.opening, { what: 'ServerScope' })); go(cfg.links.serverscope, true); } },
        roadmap: () => go(cfg.links.roadmap),
        cv: () => go(cfg.links.cv),
        ls: () => line(t.ls),
        cd: ([to = '~']) => {
            const map = { '~': cfg.links.home, '/': cfg.links.home, werk: cfg.links.work, work: cfg.links.work, notities: cfg.links.notes, notes: cfg.links.notes, roadmap: cfg.links.roadmap };
            map[to.replace(/\/$/, '')] ? go(map[to.replace(/\/$/, '')]) : line(t.cd_usage);
        },
        clear: () => { out.textContent = ''; },
        exit: () => root.closest('dialog')?.close(),
        sudo: () => line(t.sudo),
        rm: () => line(t.rm),
        coffee: () => line(t.coffee),
        stamp: () => document.querySelector('[data-stamp]')?.click(),
    };
    commands.project = commands.open;
    commands.about_me = commands.about;

    const run = async (raw) => {
        const text = raw.trim();
        line(`% ${text}`, 'term-cmd');
        if (!text) return;
        history.push(text);
        cursor = history.length;
        const [cmd, ...args] = text.split(/\s+/);
        const fn = commands[cmd.toLowerCase()];
        // ask/idea keep the original casing of their text
        fn ? await fn(['ask', 'idea'].includes(cmd.toLowerCase()) ? args : args.map((a) => a.toLowerCase())) : line(fill(t.not_found, { cmd }), 'term-err');
    };

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const value = input.value;
        input.value = '';
        run(value);
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowUp' && history.length) {
            e.preventDefault();
            cursor = Math.max(0, cursor - 1);
            input.value = history[cursor] ?? '';
        } else if (e.key === 'ArrowDown' && history.length) {
            e.preventDefault();
            cursor = Math.min(history.length, cursor + 1);
            input.value = history[cursor] ?? '';
        } else if (e.key === 'Tab' && input.value && !input.value.includes(' ')) {
            const match = Object.keys(commands).filter((c) => c.startsWith(input.value.toLowerCase()) && !c.includes('_'));
            if (match.length) { e.preventDefault(); input.value = match.length === 1 ? match[0] + ' ' : input.value; if (match.length > 1) line(match.join('  '), 'term-muted'); }
        }
    });

    root.querySelectorAll('[data-term-cmd]').forEach((chip) => chip.addEventListener('click', () => { run(chip.dataset.termCmd); input.focus({ preventScroll: true }); }));
    out.addEventListener('click', () => { if (!String(window.getSelection())) input.focus({ preventScroll: true }); });
}

export function initTerminal() {
    document.querySelectorAll('[data-terminal]').forEach(setup);

    document.addEventListener('keydown', (e) => {
        if (!(e.key === 'k' && (e.metaKey || e.ctrlKey))) return;
        e.preventDefault();
        const inline = document.querySelector('#play [data-terminal]');
        if (inline) {
            inline.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'center' });
            inline.querySelector('[data-term-input]').focus({ preventScroll: true });
            return;
        }
        const dialog = document.getElementById('terminal-dialog');
        if (dialog && !dialog.open) {
            dialog.showModal();
            dialog.querySelector('[data-term-input]')?.focus();
        }
    });
}
