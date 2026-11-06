import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Flip } from 'gsap/Flip';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger, Flip);

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
const css = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim().replace(/\s+/g, ',');

export function initMotion() {
    nav();
    featureSwitcher();
    projectFilter();
    heartbeat();

    if (reduced) {
        document.documentElement.classList.remove('js-motion');
        return;
    }

    smoothScroll();
    splitWords();
    progressBar();
    boot().then(() => {
        heroIn();
        reveals();
        counters();
        ring();
        timeline();
        drawLines();
        scramble();
    });
    backgroundCanvas();

    if (finePointer) {
        cursorGlow();
        spotlight();
        tilt();
        magnetic();
        imageParallax();
    }

    document.documentElement.classList.add('motion-ready');
}

/* ---------- smooth scroll ---------- */
let lenis;

function smoothScroll() {
    lenis = new Lenis({ duration: 1.1, easing: (t) => 1 - Math.pow(1 - t, 4) });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    // Arriving from another page with a hash (e.g. "/#work"): land on it once layout has settled.
    if (location.hash.length > 1) {
        const target = document.getElementById(location.hash.slice(1));
        if (target) window.addEventListener('load', () => lenis.scrollTo(target, { offset: -72, immediate: true }), { once: true });
    }

    $$('a[href^="#"], a[href*="/#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const url = new URL(a.href, location.href);
            if (url.pathname !== location.pathname || !url.hash) return;
            const target = document.querySelector(url.hash);
            if (!target) return;
            e.preventDefault();
            lenis.scrollTo(target, { offset: -72 });
            history.replaceState(null, '', url.hash);
        });
    });
}

function progressBar() {
    const bar = document.querySelector('[data-progress]');
    if (!bar) return;
    gsap.to(bar, { scaleX: 1, ease: 'none', scrollTrigger: { start: 0, end: 'max', scrub: 0.3 } });
}

/* ---------- boot sequence (once per session, skippable) ---------- */
function boot() {
    const el = document.querySelector('[data-boot]');
    if (!el) return Promise.resolve();

    let seen = false;
    try { seen = sessionStorage.getItem('booted') === '1'; } catch {}

    if (seen) {
        el.remove();
        return Promise.resolve();
    }

    const lines = $$('[data-boot-line]', el);
    const pulse = el.querySelector('[data-boot-pulse]');

    return new Promise((resolve) => {
        const tl = gsap.timeline({ onComplete: finish });

        if (pulse) {
            const len = pulse.getTotalLength();
            tl.fromTo(pulse, { strokeDasharray: len, strokeDashoffset: len }, { strokeDashoffset: 0, duration: 0.5, ease: 'power2.inOut' });
        }

        lines.forEach((line) => {
            const text = line.dataset.bootLine;
            const out = line.querySelector('[data-boot-text]');
            const ok = line.querySelector('[data-boot-ok]');
            tl.set(line, { opacity: 1 })
                .to({ n: 0 }, {
                    n: text.length,
                    duration: Math.min(text.length * 0.012, 0.26),
                    ease: 'none',
                    onUpdate() { out.textContent = text.slice(0, Math.round(this.targets()[0].n)); },
                })
                .set(ok, { opacity: 1 }, '+=0.03');
        });
        tl.to(el, { clipPath: 'inset(0 0 100% 0)', duration: 0.6, ease: 'expo.inOut' }, '+=0.15');

        const skip = () => tl.progress(1);
        window.addEventListener('keydown', skip, { once: true });
        el.addEventListener('click', skip, { once: true });

        function finish() {
            try { sessionStorage.setItem('booted', '1'); } catch {}
            window.removeEventListener('keydown', skip);
            el.remove();
            resolve();
        }
    });
}

/* ---------- headings: word-by-word with blur ---------- */
function splitWords() {
    $$('[data-split]').forEach((el) => {
        const words = el.textContent.trim().split(/\s+/);
        el.setAttribute('aria-label', el.textContent.trim());
        el.innerHTML = words
            .map((w) => `<span class="word inline-block overflow-hidden align-bottom pb-[0.1em]" aria-hidden="true"><span class="inline-block">${w}</span></span>`)
            .join(' ');
    });
}

function heroIn() {
    const hero = document.querySelector('[data-hero]');
    if (!hero) return;

    const tl = gsap.timeline({ defaults: { ease: 'expo.out' } });
    tl.to($$('[data-split] .word > span', hero), { opacity: 1, yPercent: 0, filter: 'blur(0px)', duration: 1.2, stagger: 0.07, startAt: { yPercent: 110, filter: 'blur(8px)' } })
        .fromTo($$('.text-gradient', hero), { backgroundPosition: '100% 0' }, { backgroundPosition: '0% 0', duration: 2.2, ease: 'power2.inOut' }, 0.1)
        .to($$('[data-reveal]', hero), { opacity: 1, y: 0, scale: 1, duration: 1, stagger: 0.08, startAt: { y: 30, scale: 0.985 } }, 0.2);
}

/* ---------- scroll reveals ---------- */
function reveals() {
    $$('[data-split]').forEach((el) => {
        if (el.closest('[data-hero]')) return;
        gsap.to($$('.word > span', el), {
            opacity: 1, yPercent: 0, filter: 'blur(0px)', duration: 1, ease: 'expo.out', stagger: 0.05,
            startAt: { yPercent: 110, filter: 'blur(6px)' },
            scrollTrigger: { trigger: el, start: 'top 88%', once: true },
        });
    });

    $$('[data-reveal]').forEach((el) => {
        if (el.closest('[data-hero]')) return;
        const group = el.closest('[data-reveal-group]');
        const index = group ? $$('[data-reveal]', group).indexOf(el) : 0;
        gsap.to(el, {
            opacity: 1, y: 0, duration: 1, ease: 'expo.out', delay: (index % 6) * 0.08,
            startAt: { y: 40 },
            scrollTrigger: { trigger: group ?? el, start: 'top 86%', once: true },
        });
    });
}

/* ---------- numbers count up to their real value ---------- */
function counters() {
    $$('[data-count]').forEach((el) => {
        const end = Number(el.dataset.count);
        const decimals = Number(el.dataset.decimals ?? 0);
        if (!Number.isFinite(end)) return;
        // The real value stays in the HTML until the count actually starts, so a missed trigger never shows 0.
        const obj = { v: 0 };
        gsap.to(obj, {
            v: end, duration: 1.6, ease: 'power3.out',
            onUpdate: () => (el.textContent = obj.v.toFixed(decimals)),
            onComplete: () => (el.textContent = end.toFixed(decimals)),
            scrollTrigger: { trigger: el, start: 'top 95%', once: true },
        });
    });
}

/* ---------- health ring draws to its score ---------- */
function ring() {
    $$('[data-ring]').forEach((circle) => {
        const len = Number(circle.getAttribute('stroke-dasharray'));
        const target = Number(circle.getAttribute('stroke-dashoffset'));
        gsap.fromTo(circle, { strokeDashoffset: len }, {
            strokeDashoffset: target, duration: 1.8, ease: 'power3.inOut', delay: 0.2,
            scrollTrigger: { trigger: circle, start: 'top 95%', once: true },
        });
    });
}

/* ---------- 24h timeline fills left to right ---------- */
function timeline() {
    $$('[data-timeline]').forEach((row) => {
        gsap.from(row.children, {
            scaleY: 0, transformOrigin: 'bottom', opacity: 0, duration: 0.5, ease: 'back.out(2)', stagger: 0.03,
            scrollTrigger: { trigger: row, start: 'top 95%', once: true },
        });
    });

    $$('[data-glance]').forEach((list) => {
        gsap.from(list.children, {
            y: 12, opacity: 0, duration: 0.6, ease: 'expo.out', stagger: 0.06, delay: 0.3,
            scrollTrigger: { trigger: list, start: 'top 95%', once: true },
        });
    });
}

/* ---------- lines that draw themselves (timeline, steps) ---------- */
function drawLines() {
    $$('[data-draw-line]').forEach((line) => {
        gsap.from(line, { scaleY: 0, ease: 'none', scrollTrigger: { trigger: line.parentElement, start: 'top 75%', end: 'bottom 60%', scrub: 0.5 } });
    });
    $$('[data-steps-line]').forEach((line) => {
        gsap.from(line, { scaleX: 0, duration: 1.6, ease: 'power2.inOut', scrollTrigger: { trigger: line.parentElement, start: 'top 80%', once: true } });
    });
}

/* ---------- section labels decode like a terminal ---------- */
function scramble() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/<>_';
    $$('[data-scramble]').forEach((el) => {
        const text = el.textContent;
        ScrollTrigger.create({
            trigger: el, start: 'top 92%', once: true,
            onEnter: () => {
                const obj = { p: 0 };
                gsap.to(obj, {
                    p: 1, duration: 0.9, ease: 'none',
                    onUpdate: () => {
                        const shown = Math.floor(obj.p * text.length);
                        el.textContent = text.slice(0, shown) + text.slice(shown).replace(/\S/g, () => chars[Math.floor(Math.random() * chars.length)]);
                    },
                    onComplete: () => (el.textContent = text),
                });
            },
        });
    });
}

/* ---------- heartbeat: an ECG line that keeps beating ---------- */
function heartbeat() {
    $$('[data-heartbeat]').forEach((canvas) => {
        const ctx = canvas.getContext('2d');
        const ok = canvas.dataset.heartbeat === 'ok';
        const color = `rgb(${css(ok ? '--green' : '--yellow')})`;
        const grid = `rgba(${css('--border')},0.6)`;
        let w, h, x = 0, visible = true;
        const pts = [];

        const resize = () => {
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            w = canvas.clientWidth; h = canvas.clientHeight;
            canvas.width = w * dpr; canvas.height = h * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        };
        resize();
        window.addEventListener('resize', resize);
        new IntersectionObserver(([e]) => (visible = e.isIntersecting)).observe(canvas);

        // One beat: flat, small bump, sharp spike, dip, recovery.
        const beat = (t) => {
            const p = t % 1;
            if (p < 0.08) return Math.sin(p / 0.08 * Math.PI) * 0.12;
            if (p < 0.14) return 0;
            if (p < 0.17) return -0.15;
            if (p < 0.21) return 1;
            if (p < 0.25) return -0.35;
            if (p < 0.38) return Math.sin((p - 0.25) / 0.13 * Math.PI) * 0.18;
            return 0;
        };

        const draw = () => {
            ctx.clearRect(0, 0, w, h);
            ctx.strokeStyle = grid;
            ctx.lineWidth = 1;
            for (let gx = 0; gx < w; gx += 12) { ctx.beginPath(); ctx.moveTo(gx, 0); ctx.lineTo(gx, h); ctx.stroke(); }

            if (!pts.length) return;
            ctx.lineWidth = 2;
            ctx.lineJoin = 'round';
            ctx.shadowColor = color;
            ctx.shadowBlur = 10;
            ctx.strokeStyle = color;
            ctx.beginPath();
            pts.forEach((p, i) => (i ? ctx.lineTo(p.x, p.y) : ctx.moveTo(p.x, p.y)));
            ctx.stroke();
            ctx.shadowBlur = 0;

            const head = pts[pts.length - 1];
            ctx.fillStyle = color;
            ctx.beginPath(); ctx.arc(head.x, head.y, 3, 0, Math.PI * 2); ctx.fill();
        };

        if (reduced) {
            for (let px = 0; px < w; px += 2) pts.push({ x: px, y: h / 2 - beat(px / 110) * (h * 0.42) });
            draw();
            return;
        }

        gsap.ticker.add(() => {
            if (!visible || !w) return;
            for (let s = 0; s < 3; s++) {
                x += 1;
                const px = x % w;
                if (px === 0) pts.length = 0;
                pts.push({ x: px, y: h / 2 - beat(x / 110) * (h * 0.42) });
            }
            // Fade the oldest part so the trace looks like it's being written.
            if (pts.length > w * 0.55) pts.splice(0, pts.length - Math.floor(w * 0.55));
            draw();
        });
    });
}

/* ---------- hero canvas: dot field that glows near the cursor ---------- */
function backgroundCanvas() {
    const canvas = document.querySelector('[data-field]');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const gap = 30;
    let w, h, dpr, visible = true, t = 0;
    const mouse = { x: -9999, y: -9999 };

    const resize = () => {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = canvas.clientWidth;
        h = canvas.clientHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };
    resize();
    window.addEventListener('resize', resize);

    window.addEventListener('pointermove', (e) => {
        const r = canvas.getBoundingClientRect();
        mouse.x = e.clientX - r.left;
        mouse.y = e.clientY - r.top;
    });

    new IntersectionObserver(([e]) => (visible = e.isIntersecting)).observe(canvas);

    gsap.ticker.add(() => {
        if (!visible) return;
        t += 0.008;
        ctx.clearRect(0, 0, w, h);
        for (let x = gap / 2; x < w; x += gap) {
            for (let y = gap / 2; y < h; y += gap) {
                const wave = Math.sin(x * 0.01 + t * 2) * Math.cos(y * 0.012 + t) * 0.5 + 0.5;
                const d = Math.hypot(x - mouse.x, y - mouse.y);
                const glow = Math.max(0, 1 - d / 200);
                const a = 0.06 + wave * 0.1 + glow * 0.75;
                ctx.fillStyle = glow > 0.05 ? `rgba(110,156,247,${a})` : `rgba(161,161,170,${a})`;
                ctx.beginPath();
                ctx.arc(x, y, 1 + glow * 1.4, 0, Math.PI * 2);
                ctx.fill();
            }
        }
    });
}

/* ---------- pointer effects ---------- */
function cursorGlow() {
    const root = document.documentElement;
    const x = gsap.quickTo({ v: 0 }, 'v', { duration: 0.6, ease: 'power3.out', onUpdate() { root.style.setProperty('--gx', `${this.targets()[0].v}px`); } });
    const y = gsap.quickTo({ v: 0 }, 'v', { duration: 0.6, ease: 'power3.out', onUpdate() { root.style.setProperty('--gy', `${this.targets()[0].v}px`); } });
    window.addEventListener('pointermove', (e) => { x(e.clientX); y(e.clientY); });
}

function spotlight() {
    $$('.spotlight').forEach((el) => {
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${e.clientX - r.left}px`);
            el.style.setProperty('--my', `${e.clientY - r.top}px`);
        });
    });
}

function tilt() {
    $$('[data-tilt]').forEach((el) => {
        const max = 4;
        const rx = gsap.quickTo(el, 'rotationX', { duration: 0.5, ease: 'power3.out' });
        const ry = gsap.quickTo(el, 'rotationY', { duration: 0.5, ease: 'power3.out' });
        gsap.set(el, { transformPerspective: 900 });
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width - 0.5;
            const py = (e.clientY - r.top) / r.height - 0.5;
            rx(-py * max * 2);
            ry(px * max * 2);
        });
        el.addEventListener('pointerleave', () => { rx(0); ry(0); });
    });
}

function magnetic() {
    $$('[data-magnetic]').forEach((el) => {
        const x = gsap.quickTo(el, 'x', { duration: 0.4, ease: 'power3.out' });
        const y = gsap.quickTo(el, 'y', { duration: 0.4, ease: 'power3.out' });
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            x((e.clientX - r.left - r.width / 2) * 0.25);
            y((e.clientY - r.top - r.height / 2) * 0.35);
        });
        el.addEventListener('pointerleave', () => { x(0); y(0); });
    });
}

function imageParallax() {
    $$('[data-img-parallax]').forEach((img) => {
        gsap.fromTo(img, { yPercent: -6 }, {
            yPercent: 0, ease: 'none',
            scrollTrigger: { trigger: img.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });
}

/* ---------- nav: shrink on scroll + sliding active-section marker ---------- */
function nav() {
    const bar = document.querySelector('[data-nav]');
    if (!bar) return;

    const onScroll = () => bar.toggleAttribute('data-scrolled', window.scrollY > 24);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    const marker = bar.querySelector('[data-nav-marker]');
    const links = $$('[data-nav-link]', bar);
    if (!marker || !links.length) return;

    const move = (link) => {
        if (!link) { marker.style.opacity = '0'; return; }
        marker.style.opacity = '1';
        marker.style.width = `${link.offsetWidth}px`;
        marker.style.transform = `translateX(${link.offsetLeft}px)`;
        links.forEach((l) => l.toggleAttribute('data-active', l === link));
    };

    const sections = links
        .map((l) => document.getElementById(l.dataset.navLink))
        .filter(Boolean);

    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) move(links.find((l) => l.dataset.navLink === e.target.id));
        });
    }, { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach((s) => io.observe(s));
}

/* ---------- ServerScope case: screens follow the feature list ---------- */
function featureSwitcher() {
    const root = document.querySelector('[data-features]');
    if (!root) return;
    const items = $$('[data-feature]', root);
    const screens = $$('[data-screen]', root);

    const activate = (i) => {
        items.forEach((el, n) => el.toggleAttribute('data-active', n === i));
        screens.forEach((el, n) => el.toggleAttribute('data-active', n === i));
    };
    activate(0);

    items.forEach((item, i) => {
        item.addEventListener('click', () => activate(i));
        item.addEventListener('focus', () => activate(i));
    });

    if (reduced || !window.matchMedia('(min-width: 1024px)').matches) return;
    items.forEach((item, i) => {
        ScrollTrigger.create({
            trigger: item,
            start: 'top 60%',
            end: 'bottom 60%',
            onToggle: (self) => self.isActive && activate(i),
        });
    });
}

/* ---------- work: filter by tag with a FLIP layout animation ---------- */
function projectFilter() {
    const bar = document.querySelector('[data-filters]');
    const grid = document.querySelector('[data-project-grid]');
    if (!bar || !grid) return;
    const cards = $$('[data-project]', grid);

    bar.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-filter]');
        if (!btn) return;
        const tag = btn.dataset.filter;
        $$('[data-filter]', bar).forEach((b) => b.toggleAttribute('data-active', b === btn));

        const state = Flip.getState(cards);
        cards.forEach((card) => {
            const match = !tag || card.dataset.tags.split('|').includes(tag);
            card.style.display = match ? '' : 'none';
        });

        if (reduced) return;
        Flip.from(state, {
            duration: 0.6, ease: 'expo.out', scale: true, absolute: true,
            onEnter: (els) => gsap.fromTo(els, { opacity: 0, scale: 0.92 }, { opacity: 1, scale: 1, duration: 0.5, ease: 'expo.out' }),
            onLeave: (els) => gsap.to(els, { opacity: 0, scale: 0.92, duration: 0.3 }),
            onComplete: () => ScrollTrigger.refresh(),
        });
    });
}
