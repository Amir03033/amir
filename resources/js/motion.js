import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Flip } from 'gsap/Flip';

gsap.registerPlugin(ScrollTrigger, Flip);

// Performance rules for this file: animate only transform/opacity, never touch styles on <html>,
// and let canvases sleep when nothing changes, when they are off-screen or when the tab is hidden.

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
const css = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim().replace(/\s+/g, ',');

export function initMotion() {
    nav();
    featureSwitcher();
    projectFilter();
    heartbeat();
    hoverVideos();
    carousels();

    if (reduced) {
        document.documentElement.classList.remove('js-motion');
        return;
    }

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

    if (finePointer) {
        dotField();
        cursorGlow();
        spotlight();
        tilt();
        magnetic();
    }

    document.documentElement.classList.add('motion-ready');
}

/* Runs `draw` on animation frames, at most `fps` times per second, only while `isActive()` is true. */
function loop(draw, fps = 60, isActive = () => true) {
    const min = 1000 / fps - 1;
    let last = 0;
    const tick = (t) => {
        requestAnimationFrame(tick);
        if (document.hidden || !isActive() || t - last < min) return;
        last = t;
        draw(t);
    };
    requestAnimationFrame(tick);
}

function onScreen(el, cb) {
    new IntersectionObserver(([e]) => cb(e.isIntersecting)).observe(el);
}

function progressBar() {
    const bar = document.querySelector('[data-progress]');
    if (!bar) return;
    gsap.to(bar, { scaleX: 1, ease: 'none', scrollTrigger: { start: 0, end: 'max', scrub: true } });
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
            tl.fromTo(pulse, { strokeDasharray: len, strokeDashoffset: len }, { strokeDashoffset: 0, duration: 0.4, ease: 'power2.inOut' });
        }

        lines.forEach((line) => {
            const text = line.dataset.bootLine;
            const out = line.querySelector('[data-boot-text]');
            const ok = line.querySelector('[data-boot-ok]');
            tl.set(line, { opacity: 1 })
                .to({ n: 0 }, {
                    n: text.length,
                    duration: Math.min(text.length * 0.01, 0.2),
                    ease: 'none',
                    onUpdate() { out.textContent = text.slice(0, Math.round(this.targets()[0].n)); },
                })
                .set(ok, { opacity: 1 }, '+=0.02');
        });
        tl.to(el, { yPercent: -100, duration: 0.55, ease: 'expo.inOut' }, '+=0.1');

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

/* ---------- headings: word-by-word ---------- */
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
    tl.to($$('[data-split] .word > span', hero), { opacity: 1, yPercent: 0, rotate: 0, duration: 1.1, stagger: 0.06, startAt: { yPercent: 110, rotate: 4 } })
        .fromTo($$('.text-gradient', hero), { backgroundPosition: '100% 0' }, { backgroundPosition: '0% 0', duration: 2, ease: 'power2.inOut' }, 0.1)
        .to($$('[data-reveal]', hero), { opacity: 1, y: 0, duration: 0.9, stagger: 0.07, startAt: { y: 28 } }, 0.15);
}

/* ---------- scroll reveals (one trigger per group, not per element) ---------- */
function reveals() {
    $$('[data-split]').forEach((el) => {
        if (el.closest('[data-hero]')) return;
        gsap.to($$('.word > span', el), {
            opacity: 1, yPercent: 0, rotate: 0, duration: 0.9, ease: 'expo.out', stagger: 0.045,
            startAt: { yPercent: 110, rotate: 3 },
            scrollTrigger: { trigger: el, start: 'top 88%', once: true },
        });
    });

    const loose = [];
    const groups = new Map();
    $$('[data-reveal]').forEach((el) => {
        if (el.closest('[data-hero]')) return;
        const group = el.closest('[data-reveal-group]');
        if (group) (groups.get(group) ?? groups.set(group, []).get(group)).push(el);
        else loose.push(el);
    });

    groups.forEach((els, group) => {
        gsap.to(els, {
            opacity: 1, y: 0, duration: 0.9, ease: 'expo.out', stagger: 0.07,
            startAt: { y: 36 },
            scrollTrigger: { trigger: group, start: 'top 86%', once: true },
        });
    });

    ScrollTrigger.batch(loose, {
        start: 'top 88%',
        once: true,
        onEnter: (batch) => gsap.to(batch, { opacity: 1, y: 0, duration: 0.9, ease: 'expo.out', stagger: 0.07, startAt: { y: 36 } }),
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
            scaleY: 0, transformOrigin: 'bottom', duration: 0.5, ease: 'back.out(2)', stagger: 0.025,
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
        gsap.from(line, { scaleY: 0, duration: 1.4, ease: 'power2.inOut', scrollTrigger: { trigger: line.parentElement, start: 'top 75%', once: true } });
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
                    p: 1, duration: 0.8, ease: 'none',
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

/* ---------- heartbeat: an ECG line, 30 fps, only while visible ---------- */
function heartbeat() {
    $$('[data-heartbeat]').forEach((canvas) => {
        const ctx = canvas.getContext('2d');
        const ok = canvas.dataset.heartbeat === 'ok';
        let rgb = css(ok ? '--green' : '--yellow');
        let grid = `rgba(${css('--border')},0.6)`;
        let w = 0, h = 0, x = 0, visible = false;
        const pts = [];
        let gridLayer;

        const resize = () => {
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            w = canvas.clientWidth; h = canvas.clientHeight;
            canvas.width = w * dpr; canvas.height = h * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            // Static grid is drawn once and blitted each frame.
            gridLayer = document.createElement('canvas');
            gridLayer.width = canvas.width; gridLayer.height = canvas.height;
            const g = gridLayer.getContext('2d');
            g.setTransform(dpr, 0, 0, dpr, 0, 0);
            g.strokeStyle = grid; g.lineWidth = 1;
            g.beginPath();
            for (let gx = 0.5; gx < w; gx += 12) { g.moveTo(gx, 0); g.lineTo(gx, h); }
            g.stroke();
        };
        resize();
        window.addEventListener('resize', resize, { passive: true });
        window.addEventListener('themechange', () => {
            rgb = css(ok ? '--green' : '--yellow');
            grid = `rgba(${css('--border')},0.6)`;
            resize();
        });
        onScreen(canvas, (v) => (visible = v));

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
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(gridLayer, 0, 0);
            const dpr = canvas.width / (w || 1);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            if (!pts.length) return;

            const path = () => { ctx.beginPath(); pts.forEach((p, i) => (i ? ctx.lineTo(p.x, p.y) : ctx.moveTo(p.x, p.y))); };
            ctx.lineJoin = 'round';
            // Cheap glow: a wide translucent stroke under the sharp one (shadowBlur is far more expensive).
            path(); ctx.strokeStyle = `rgba(${rgb},0.18)`; ctx.lineWidth = 6; ctx.stroke();
            path(); ctx.strokeStyle = `rgb(${rgb})`; ctx.lineWidth = 2; ctx.stroke();

            const head = pts[pts.length - 1];
            ctx.fillStyle = `rgb(${rgb})`;
            ctx.beginPath(); ctx.arc(head.x, head.y, 3, 0, Math.PI * 2); ctx.fill();
        };

        if (reduced) {
            for (let px = 0; px < w; px += 2) pts.push({ x: px, y: h / 2 - beat(px / 110) * (h * 0.42) });
            draw();
            return;
        }

        loop(() => {
            if (!w) return;
            for (let s = 0; s < 6; s++) {
                x += 1;
                const px = x % w;
                if (px === 0) pts.length = 0;
                pts.push({ x: px, y: h / 2 - beat(x / 110) * (h * 0.42) });
            }
            if (pts.length > w * 0.55) pts.splice(0, pts.length - Math.floor(w * 0.55));
            draw();
        }, 30, () => visible);
    });
}

/* ---------- hero dot field: static grid, only the area around the cursor is redrawn ---------- */
function dotField() {
    const canvas = document.querySelector('[data-field]');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const gap = 32;
    const radius = 180;
    let w = 0, h = 0, dpr = 1, base, visible = false, dirty = true;
    const mouse = { x: -9999, y: -9999 };

    const resize = () => {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = canvas.clientWidth; h = canvas.clientHeight;
        canvas.width = w * dpr; canvas.height = h * dpr;
        base = document.createElement('canvas');
        base.width = canvas.width; base.height = canvas.height;
        const b = base.getContext('2d');
        b.setTransform(dpr, 0, 0, dpr, 0, 0);
        b.fillStyle = 'rgba(161,161,170,0.16)';
        for (let x = gap / 2; x < w; x += gap) for (let y = gap / 2; y < h; y += gap) b.fillRect(x - 0.75, y - 0.75, 1.5, 1.5);
        dirty = true;
    };
    resize();
    window.addEventListener('resize', resize, { passive: true });
    onScreen(canvas, (v) => { visible = v; dirty = true; });

    window.addEventListener('pointermove', (e) => {
        if (!visible) return;
        const r = canvas.getBoundingClientRect();
        mouse.x = e.clientX - r.left;
        mouse.y = e.clientY - r.top;
        dirty = true;
    }, { passive: true });

    loop(() => {
        if (!dirty) return;
        dirty = false;
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(base, 0, 0);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        const x0 = Math.max(gap / 2, Math.floor((mouse.x - radius) / gap) * gap + gap / 2);
        const y0 = Math.max(gap / 2, Math.floor((mouse.y - radius) / gap) * gap + gap / 2);
        for (let x = x0; x < Math.min(w, mouse.x + radius); x += gap) {
            for (let y = y0; y < Math.min(h, mouse.y + radius); y += gap) {
                const glow = 1 - Math.hypot(x - mouse.x, y - mouse.y) / radius;
                if (glow <= 0) continue;
                const s = 1.5 + glow * 2.5;
                ctx.fillStyle = `rgba(110,156,247,${0.15 + glow * 0.75})`;
                ctx.fillRect(x - s / 2, y - s / 2, s, s);
            }
        }
    }, 60, () => visible);
}

/* ---------- pointer effects ---------- */
function cursorGlow() {
    const glow = document.querySelector('.cursor-glow');
    if (!glow) return;
    const x = gsap.quickTo(glow, 'x', { duration: 0.6, ease: 'power3.out' });
    const y = gsap.quickTo(glow, 'y', { duration: 0.6, ease: 'power3.out' });
    window.addEventListener('pointermove', (e) => { x(e.clientX); y(e.clientY); }, { passive: true });
}

function spotlight() {
    $$('.spotlight').forEach((el) => {
        let frame = 0, ex = 0, ey = 0;
        el.addEventListener('pointermove', (e) => {
            ex = e.clientX; ey = e.clientY;
            if (frame) return;
            frame = requestAnimationFrame(() => {
                frame = 0;
                const r = el.getBoundingClientRect();
                el.style.setProperty('--mx', `${ex - r.left}px`);
                el.style.setProperty('--my', `${ey - r.top}px`);
            });
        }, { passive: true });
    });
}

function tilt() {
    $$('[data-tilt]').forEach((el) => {
        const max = 4;
        const rx = gsap.quickTo(el, 'rotationX', { duration: 0.5, ease: 'power3.out' });
        const ry = gsap.quickTo(el, 'rotationY', { duration: 0.5, ease: 'power3.out' });
        let rect;
        gsap.set(el, { transformPerspective: 900 });
        el.addEventListener('pointerenter', () => (rect = el.getBoundingClientRect()), { passive: true });
        el.addEventListener('pointermove', (e) => {
            rect ??= el.getBoundingClientRect();
            rx(-((e.clientY - rect.top) / rect.height - 0.5) * max * 2);
            ry(((e.clientX - rect.left) / rect.width - 0.5) * max * 2);
        }, { passive: true });
        el.addEventListener('pointerleave', () => { rect = null; rx(0); ry(0); });
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
        }, { passive: true });
        el.addEventListener('pointerleave', () => { x(0); y(0); });
    });
}

/* ---------- project videos: hover on desktop, in view on touch; nothing loads until needed ---------- */
function hoverVideos() {
    $$('[data-hover-video]').forEach((video) => {
        const card = video.closest('[data-project]') ?? video.parentElement;
        const play = () => { video.play().then(() => (video.style.opacity = '1')).catch(() => {}); };
        const stop = () => { video.pause(); video.style.opacity = '0'; };

        if (reduced) return;

        if (finePointer) {
            card.addEventListener('pointerenter', play);
            card.addEventListener('pointerleave', stop);
        } else {
            new IntersectionObserver(([e]) => (e.intersectionRatio > 0.6 ? play() : stop()), { threshold: [0, 0.6] }).observe(card);
        }
    });
}

/* ---------- nav: shrink on scroll + sliding active-section marker ---------- */
function nav() {
    const bar = document.querySelector('[data-nav]');
    if (!bar) return;

    let scrolled = null;
    const onScroll = () => {
        const s = window.scrollY > 24;
        if (s !== scrolled) bar.toggleAttribute('data-scrolled', (scrolled = s));
    };
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

/* ---------- swipe carousels (ServerScope features on phones): counter + dots follow the swipe ---------- */
function carousels() {
    $$('[data-carousel]').forEach((root) => {
        const track = root.querySelector('[data-carousel-track]');
        const slides = $$('[data-carousel-slide]', root);
        const dots = $$('[data-carousel-dot]', root);
        const current = root.querySelector('[data-carousel-current]');
        if (!track || !slides.length) return;

        let active = -1;
        const set = (i) => {
            if (i === active) return;
            active = i;
            slides.forEach((el, n) => el.toggleAttribute('data-active', n === i));
            dots.forEach((el, n) => el.toggleAttribute('data-active', n === i));
            if (current) current.textContent = i + 1;
        };

        // The slide whose centre is closest to the track's centre is the active one.
        let frame = 0;
        track.addEventListener('scroll', () => {
            if (frame) return;
            frame = requestAnimationFrame(() => {
                frame = 0;
                const mid = track.scrollLeft + track.clientWidth / 2;
                let best = 0;
                slides.forEach((el, n) => {
                    if (Math.abs(el.offsetLeft + el.offsetWidth / 2 - mid) < Math.abs(slides[best].offsetLeft + slides[best].offsetWidth / 2 - mid)) best = n;
                });
                set(best);
            });
        }, { passive: true });

        dots.forEach((dot, n) => dot.addEventListener('click', () => {
            const el = slides[n];
            track.scrollTo({ left: el.offsetLeft - (track.clientWidth - el.offsetWidth) / 2, behavior: reduced ? 'auto' : 'smooth' });
        }));

        set(0);
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
