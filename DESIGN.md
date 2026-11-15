# amirs.life · Design system

Status: **draft for approval**. Nothing here is built yet. Decisions marked **[CHOOSE]** need your answer.

Rule for the whole document: amirs.life is the personal side. Jebbari is the polished, minimal agency and ServerScope is the dark dashboard. I did not open jebbari.nl, ServerScope or ServerScope-Info for styling. Everything below is derived from the brief and from what this repo currently looks like. The current site *is* the ServerScope look (the comment in `tailwind.config.js` says "Shared with ServerScope: same palette, radius and type"), so the audit below also covers what we are moving away from.

> `design-refs/` does not exist: not in the working tree, on any branch, or in git history. If you meant to add references, push them and I'll fold them in before Phase 1.

---

## 0. Audit: what looks generic now, and why

| # | What | Where | Why it reads as generic / AI-made |
|---|------|-------|-----------------------------------|
| 1 | Zinc-black page, blue `#6e9cf7` accent, green status dots | `resources/css/app.css` `:root` | It's ServerScope's palette, and also the default "dev dashboard" palette. |
| 2 | Radial blue/green glow behind the page plus a 600px cursor glow | `layouts/layout.blade.php`, `.cursor-glow` | These are the glowing orbs from your sloppy list. |
| 3 | `.text-gradient` on the name (text → blue → green sweep) | hero | Gradient text is from the sloppy list. |
| 4 | `.spotlight` cards with a cursor-following gradient border | everywhere | Generic SaaS card effect, applied to every panel. |
| 5 | `rounded-panel` 18px + border + panel bg on *everything* (cards, forms, lists, links) | all sections | One uniform card treatment, the "uniform rounded + shadow" look. |
| 6 | Three identical cards in a row: decisions, stack groups, 4 process steps, 3 testimonials | home | Three identical cards in a row, from your list. |
| 7 | Inter + JetBrains Mono | `app.js` | Default developer pairing, and shared with ServerScope. |
| 8 | Fake-browser chrome (three grey dots, URL pill) around screenshots | ServerScope block, case page | Overused mock-up shell; it hides the actual work. |
| 9 | "Proof, not promises" stat band (live projects, uptime %, notes count, avg ms) with count-up | `#proof` | The numbers are real but small, and a count-up stat band *reads* as fake stats. |
| 10 | "Understand → Build → Deliver → Monitor" numbered process cards | `#proof` | Agency boilerplate. It belongs to Jebbari, not to your personal site. |
| 11 | Boot screen ("booting amir-jebbari.portfolio…"), canvas dot field, heartbeat canvas | `motion.js` | Hacker-terminal cliché. It costs main-thread time and delays content. |
| 12 | Infinite marquee of tech chips with coloured dots | `#stack` | Logo-wall filler that carries no information. |
| 13 | Centred 404 card with "incident · 404 not found" | `errors/404` | Dashboard metaphor again. |
| 14 | Copy: "Developer die oplevert", "Laten we iets bouwen", "Proof, not promises", "Why work with me" | `lang/*` | Job-seeker / agency tone. It also contradicts the new positioning (founder of Jebbari, Zwolle & Amsterdam). The current copy says "18, student in Overijssel, open to internships". |
| 15 | Project fallback images | `public/images/projects/` | `klusklaar.webp` is a stock photo of tools. `jungletuin.webp` is a stock-looking garden photo. `mastermind.webp` is a **purple gradient with sparkles**. None of the three is a screenshot. |
| 16 | GSAP + Vue + vue-router in `package.json` | | Vue is unused. GSAP is used for effects CSS can do. |

What's worth keeping (behaviour, not looks): locale routing (`/` + `/en`), `lroute()` / `alternate_url()`, the contact flow (honeypot, DB-first, mail fallback), TrackVisit, the Setting store with `now.nl` / `now.en`, OG image generation, the sitemap with hreflang, and View Transitions already turned on.

---

## 1. Personality

**Confident, characterful, personal. Precise craft with a human touch.**

- Jebbari = gallery wall: white space, restraint, centred.
- amirs.life = your desk: paper, ink, a rubber stamp, notes pinned up, a terminal left open. It's editorial and asymmetric, with big type that has an opinion.
- Voice: first person, short sentences, a bit of humour, no superlatives. Write it the way you'd say it out loud.

Same person, different space: the only shared things are your name, links to Jebbari and ServerScope, and one line in the footer and about section.

---

## 2. Colour: two directions **[CHOOSE A or B]**

Both have a light mode as the hero and a separately designed dark mode. Neither uses blue, teal, purple or gradients. Contrast ratios below were computed (WCAG 2.x), not guessed.

### Direction A: "Paper, Ink & Vermilion" *(my recommendation)*

Warm paper, near-black ink, and one vermilion accent, like a red stamp on a manuscript. It's the clearest break from both the dark ServerScope dashboard and a white minimal agency site. Vermilion is warm and personal and has nothing in common with a blue/teal tech palette. Dark mode is a "darkroom": warm brown-black, with paper-coloured type and a brighter vermilion.

Primary buttons are **ink**, not vermilion: paper text on vermilion is only 3.9:1. That keeps the accent rare, which is the point.

**Light (hero)**

| Token | Hex | Use | Contrast on `--bg` |
|---|---|---|---|
| `--n-50` | `#FBF8F2` | raised surfaces, inputs | |
| `--n-100` | `#F3EEE4` | **page background** | |
| `--n-200` | `#E9E2D4` | row hover, subtle fills, code bg | |
| `--n-300` | `#D6CDBB` | hairlines | 1.36 (decorative) |
| `--n-400` | `#B5AA95` | strong lines, disabled borders | |
| `--n-500` | `#8A806E` | large numerals, icons ≥24px only | 3.37 |
| `--n-600` | `#665D4E` | muted text, mono labels | **5.60** |
| `--n-700` | `#4A4338` | secondary text | **8.44** |
| `--n-800` | `#2D2820` | | |
| `--n-900` | `#1A1712` | **ink: body text, primary button** | **15.45** |
| `--accent` | `#E2431E` | non-text and large text only: stamp, focus ring, active marks, underline stroke | 3.59 |
| `--accent-text` | `#B8320F` | small accent text (links in body, active nav item) | **5.19** |

**Dark (alternative): "darkroom"**

| Token | Hex | Use | Contrast on `--bg` |
|---|---|---|---|
| `--n-50` | `#0E0C09` | footer, deepest | |
| `--n-100` | `#14110D` | **page background** | |
| `--n-200` | `#1D1914` | raised surfaces, inputs | |
| `--n-300` | `#2A251E` | row hover, fills | |
| `--n-400` | `#3A332A` | hairlines | 1.51 |
| `--n-500` | `#5A5145` | strong lines | |
| `--n-600` | `#A3988A` | muted text, labels | **6.65** |
| `--n-700` | `#CFC5B3` | secondary text | |
| `--n-800` | `#E4DCCB` | | |
| `--n-900` | `#EEE6D6` | **text** (it's the paper colour) | **15.17** |
| `--accent` / `--accent-text` | `#FF6B3D` | brighter vermilion, safe as text here | **6.65** |

What changes in dark beyond the swap: photos get `filter: saturate(.9) contrast(1.05)`, the stamp prints in accent on `--n-200` instead of on paper, hairlines are a touch more visible (1.5 vs 1.36), and the marquee strip flips to paper-on-ink.

### Direction B: "Bone, Moss & Lime"

A pale sage-bone paper, deep moss-black ink, and an electric lime used as a **fill** (a highlighter swipe behind ink text, focus ring, active pill), never as text on light. Fresher and more "studio". The risk: lime sits closer to ServerScope's green status dots, and highlighter-lime is fashionable right now.

| Token | Light | Dark |
|---|---|---|
| `--bg` | `#ECEDE4` | `#0E1410` |
| raised | `#F6F6F0` | `#151C17` |
| fill / hover | `#E0E2D6` | `#1D2620` |
| hairline | `#CDD1C4` (1.32) | `#2A332D` (1.43) |
| strong line | `#AEB4A5` | `#435047` |
| muted text | `#59635C` (**5.29**) | `#9AA59C` (**7.31**) |
| secondary | `#3B4540` (**8.43**) | `#C9D0C5` |
| text | `#131B16` (**14.88**) | `#E4E9DE` (**15.10**) |
| `--accent` | `#C6EE3A` as fill, ink on top **13.1** | `#C6EE3A` as text **13.9** |

### Semantic colours (forms only)

| | A light | A dark | B light | B dark |
|---|---|---|---|---|
| error | `#9E1B32` oxblood (6.84) | `#FF8A8A` (8.29) | `#A3222E` (6.30) | `#FF8A8A` |
| success | `#2E6A3B` (5.60) | `#7FD08F` (10.15) | `#2B6B3F` (5.43) | `#7FD08F` |
| warning | `#8A5A00` | `#E8B44A` | same | same |

In A, error (oxblood) and accent (vermilion) are close in hue, so errors never rely on colour alone: they always carry an icon (`circle-alert`) and text, and the field gets a 2px left rule.

### Semantic token layer (used by components, defined per theme)

```css
--bg --surface --surface-2 --fill
--line --line-strong
--text --text-2 --muted
--accent --accent-text --on-accent
--focus (= --accent) --ok --err --warn
--selection (accent @ 22%)
```

Components only ever reference semantic tokens. Themes remap them, so dark mode is a separate design rather than an inversion.

---

## 3. Typography

Self-hosted variable WOFF2 via Fontsource (latin subset only), `font-display: swap`, display face preloaded. Neither is Inter or JetBrains Mono. **Please confirm that Jebbari doesn't use either family**; I deliberately didn't look.

| Role | Family | Why |
|---|---|---|
| Display + body | **Bricolage Grotesque Variable** (opsz 12–96, wdth 75–100, wght 200–800) | A grotesk with real character: ink traps and quirky details at large optical sizes, calm and readable at text size. One family covers both roles, so there's one file to preload. |
| Mono labels, terminal | **Martian Mono Variable** (wdth 75–112.5, wght 100–800) | Distinctive, slightly wide mono. Used narrow (`wdth 87`) and uppercase for labels. |

Payload budget: Bricolage latin ≈ 70–90 KB, Martian Mono latin ≈ 40–60 KB (loaded without preload, only used below the fold or in small labels). The hero is fully readable with fallback metrics: `size-adjust` is tuned on a local fallback (`Arial`/`Helvetica`) so the swap causes no CLS.

### Fluid scale (between 390px and 1440px viewports)

| Token | Size | Line height | Tracking | Settings |
|---|---|---|---|---|
| `--t-hero` | `clamp(3.5rem, 2.1rem + 5.9vw, 7.5rem)` → **56–120px** | 0.95 | -0.035em | wght 720, wdth 82, opsz 96 |
| `--t-d1` (section titles) | `clamp(2.5rem, 1.7rem + 3.3vw, 4.75rem)` → 40–76 | 1.0 | -0.03em | wght 680, wdth 88 |
| `--t-d2` (project names, case titles) | `clamp(1.75rem, 1.2rem + 2.3vw, 3.25rem)` → 28–52 | 1.02 | -0.025em | wght 620, wdth 90 |
| `--t-h3` | `clamp(1.25rem, 1.1rem + .6vw, 1.625rem)` → 20–26 | 1.15 | -0.01em | wght 600 |
| `--t-lead` | `clamp(1.125rem, 1.05rem + .3vw, 1.3125rem)` → 18–21 | 1.5 | 0 | wght 400 |
| `--t-body` | `clamp(1.0625rem, 1.03rem + .12vw, 1.125rem)` → **17–18** | **1.6** | 0 | wght 400, opsz auto |
| `--t-small` | 0.9375rem → 15 | 1.5 | 0 | |
| `--t-label` | `clamp(.75rem, .73rem + .08vw, .8125rem)` → **12–13** | 1.3 | **+0.12em**, uppercase | Martian Mono wght 450, wdth 87 |
| `--t-numeral` | `clamp(6rem, 3rem + 12vw, 13rem)` → 96–208 | 0.8 | -0.05em | wght 800, wdth 75, colour `--n-300`/`--n-400` |

Body text max width: `68ch`. Use `tabular-nums` for all times, years and counts. `text-wrap: balance` on headings, `pretty` on paragraphs.

---

## 4. Layout language

- **Grid:** 12 columns, content max **1200px**, outer gutter 16px (mobile) → 24px (≥768) → 40px (≥1280), column gap 24px.
- **Asymmetry by default:** text starts at column 3 or 4, never centred. Columns 1–2 hold a **sticky side label** (mono, `01 — Werk`) that stays pinned while the section scrolls.
- **Oversized numerals:** each home section has its index (`01`…`07`) at `--t-numeral` in a hairline colour, bleeding off the left edge on desktop and sitting above the title on mobile.
- **Section spacing:** `--space-section: clamp(96px, 6rem + 6vw, 200px)`. Sections are separated by a full-bleed hairline, not by background colour changes.
- **Strip details:** one marquee strip under the hero (mono, ink band with the status items). It's the only marquee, it pauses on hover and focus, and it's static under reduced motion.
- **Offset images:** images sit off-grid by one column or overlap a hairline. The about photo is offset down 64px against its text column, with a mono caption.
- **Hairlines, not shadows.** No `box-shadow` anywhere except the one "print" offset (`3px 3px 0 var(--n-900)`, no blur), reserved for the primary button's pressed state and the idea notes.
- **Spacing scale (4px base):** 4, 8, 12, 16, 24, 32, 48, 64, 96, 128.
- **Radius:** exactly three values. `--r-0: 0` (images, rows, sections, panels), `--r-1: 6px` (buttons, inputs, toasts, modal), `--r-pill: 999px` (tags, toggles, status dot). Tailwind's default radius and shadow scales are removed from the config, so `rounded-2xl` / `shadow-lg` can't creep back in.
- **Z-index:** nav 50, cursor 60, sheet/modal 70, toast 80, skip link 90.

---

## 5. Components (designed once, reused)

| Component | Spec |
|---|---|
| **Nav** | Full: wordmark `amir·s life` left (Bricolage 700, the middle dot in accent), 4 links (Werk, Over, Speeltuin, Contact) in mono labels, theme + language on the right. After 80px of scroll it **compacts**: height 72 → 52, wordmark shortens to `a·`, background becomes `--bg` at 92% with a bottom hairline (no blur, no glass). **Mobile:** a "Menu" text button opens a **full-screen sheet** in ink (paper in dark) with huge Bricolage links (`--t-d1`) numbered 01–05, a status line at the bottom, focus-trapped, closed with Esc. Active section gets an accent underline. |
| **Buttons** | *Primary:* ink fill, paper text, 6px radius, 48px tall, mono label or Bricolage 600. Hover shifts −1px/−1px and shows a hard 3px offset (magnetic pull ≤6px on fine pointers). Active snaps back to 0. *Secondary:* transparent, 1px `--line-strong` border, ink text; hover fills `--fill`. *Text link:* inherits colour, underline drawn with a `background-size` transition from 0→100% (2px, `--accent`), 300ms, left to right; it retracts right on leave. External links get the Lucide `arrow-up-right` icon. |
| **Tag** | Mono label, 1px `--line-strong` border, pill radius, 4px 10px padding. No fill and no colour coding. |
| **Status line** | Mono label row with a dot, `AMS 14:32`, `bezig met: …` / `building: …`. The time updates every minute via JS; the server renders the correct initial time, so there's no flash. |
| **Section header** | Sticky side label + oversized numeral + `--t-d1` title + optional lead. Title spans columns 3–10. |
| **Project row** | Full-width row in a ruled list: `year` (mono) · **name** (`--t-d2`) · one-line description · stack tags · `→`. Desktop hover: the sibling rows dim to 35%, the name shifts 8px right, and a **preview image follows the cursor** (300×200, `--r-0`, slight rotation ±2° by velocity, lazy-loaded on first hover). Touch: no preview; the thumbnail shows inline under the name at 390px. The whole row is one link, and keyboard focus shows the same state as hover. |
| **Case-study layout** | Header: name (`--t-hero` scaled to 0.8), one-liner, meta grid (Year / Role / Stack / Link) in mono. Then sticky side labels for **Problem → What I built → Result**, each a 6–7 column text block. Screenshots: full-bleed crop or "clean crop" (no fake browser chrome); phone shots in a simple 1px device outline. Gallery: a horizontal strip with snap on mobile and a 2-up offset grid on desktop. Footer: **previous / next** as two big halves with project names. |
| **Certificate item** | One compact ruled row: `2025 · Title · Issuer · ↗`. No icons, no cards. |
| **Blog row** | `date (mono) · title (h3) · reading time`. Hover underline on the title. |
| **Form fields** | Label above (mono label), input with only a 1px bottom border on `--surface` and 6px top radius, 52px tall, 17px text. Focus: bottom border becomes 2px `--accent` plus a 2px focus ring offset. Error: 2px left rule in `--err`, icon + message below, `aria-describedby`. Help text in `--muted`. Textarea autosizes up to 12 rows. Submit button shows a spinner and stays disabled while sending. |
| **Toast** | Bottom-left (bottom on mobile), ink bg / paper text, 6px radius, mono label + message, icon `check` / `circle-alert`. 4s, pause on hover, `role="status"` (`alert` for errors), at most 3 stacked, closable with Esc. |
| **Modal / sheet** | Desktop: centred panel max 560px, hairline border, `--surface`. Mobile: bottom sheet with a drag handle. `<dialog>` element, focus trap, Esc, scrim `rgb(ink / .4)`, no blur. |
| **Idea note** | Paper note (`--n-50`), hairline border, hard offset, ±1.5° rotation derived from the idea id (deterministic, no CLS). Shows title, status tag and vote button (`arrow-up` + count). Voted state: accent outline. |
| **Question item** | Q in Bricolage 600, A below in body, with the date and "— Amir" in mono. Unanswered questions are never public. |
| **Terminal** | Ink panel (paper text) in both themes, Martian Mono 14px, prompt `amir@life:~$` with the accent `$`. A **visible `<input>`** always sits at the bottom (mobile keyboards work), plus command chips under it (`help` `projects` `ask` …) for touch. History with ↑/↓, Tab completion, `aria-live="polite"` on the output. |
| **Footer** | Big email link (`--t-d2`) · links (Werk, Notities, Roadmap, Nu, Privacy, CV) · language switch · theme toggle · "Gebouwd met Laravel, door mij." · "Ook: Jebbari ↗" · `© 2026`. Three rows at most. |
| **404** | Huge `404` numeral (outline), a line in your voice ("Deze pagina bestaat niet. Of nog niet."), the terminal prompt pre-filled with `cd ~`, and links home / work. |
| **500** | Same shell, no DB calls, inline CSS fallback so it renders even if Vite assets are missing. Text: "Er ging iets mis aan mijn kant." |
| **Empty / loading / error states** | Empty: one sentence plus an action ("Nog geen ideeën. Wees de eerste."). Loading: skeleton hairline rows (no shimmer gradients, a 1s opacity pulse). Error: inline message with a retry text button. |
| **Theme toggle** | A text button `licht / donker / systeem` that cycles, with a sun / moon / monitor icon. |
| **Language switch** | `NL / EN` in mono, the current one in ink and the other muted. It links to `alternate_url()`. |

**Icons:** Lucide (from `lucide-static`), inlined as SVG via one `<x-icon name="…" />` Blade component that reads a whitelisted set at build time. `stroke-width: 1.5`, `currentColor`, sizes 16 / 20 / 24. Never emoji: the current `☾ / ☀` in the palette goes.

---

## 6. Interactive hero element **[CHOOSE]**

**H1: "The stamp" (my recommendation).** A round vermilion rubber stamp (circular mono text `AMIRS.LIFE · ZWOLLE · AMSTERDAM ·` around an `a·` monogram) sits off-grid next to the headline. On desktop it turns slowly toward the cursor with a spring. Click (or tap on touch) **stamps** an imprint where you clicked: a slightly rotated, semi-transparent copy that fades after a few seconds, max 8 on screen. It's personal, tactile, and is the hero's single accent detail. It only uses transforms, is one element, and costs ~1.5 KB of JS. Under reduced motion it's static and a click stamps without the animation.

**H2: "Halftone me."** Your photo rendered as a halftone dot grid on a `<canvas>`; dots near the cursor/finger grow and shift. More personal, but it needs a photo cut out from its background (the current photos are on a busy bridge), and it costs more CPU.

---

## 7. Motion

| Token | Value | Use |
|---|---|---|
| `--ease-out` | `cubic-bezier(0.22, 1, 0.36, 1)` | all entrances |
| `--ease-spring` | CSS `linear()` spring approximation (≈10% overshoot); falls back to `--ease-out` | hovers, stamp, magnetic return |
| `--d-ui` | 220ms | hovers, toggles, toasts |
| `--d-ui-slow` | 300ms | nav compact, sheet |
| `--d-reveal` | 700ms | section reveals |
| `--d-hero` | 900ms | hero lines |
| `--stagger` | 70ms | lists, reveal groups |

- **Hero:** line-by-line mask reveal (each line in `overflow:clip`, inner text from `translateY(100%)` to 0, 900ms, 80ms stagger). It's **pure CSS on first paint**, not waiting on JS, so the LCP text paints within the first ~900ms. The role line and status line follow at +200ms.
- **Sections:** fade + `translateY(16px)` once, 700ms, staggered 70ms, triggered by one shared IntersectionObserver. Content is visible by default; the hidden start state is only applied when JS and motion are both available (the current `js-motion` guard and its 2.5s failsafe stay).
- **Parallax:** only the about photo and the case-study hero image, max 40px, via CSS scroll-driven animations (`animation-timeline: view()`). Zero JS; static where unsupported.
- **Hover:** link underline draws in; buttons shift 1–2px with a magnetic pull ≤6px on `(hover:hover) and (pointer:fine)`; project rows dim their siblings; tags don't move.
- **Page transitions:** cross-document View Transitions (`@view-transition { navigation: auto }`). The old page fades out in 150ms, the new one fades in + 12px slide in 250ms. Browsers without support just navigate.
- **Custom cursor:** desktop and fine pointer only. The **native cursor is never hidden**; we add a small trailing ring (12px, 1px ink) that grows to 64px with a mono "bekijk / view" label over project rows. It hides over inputs, textareas and the terminal, and is never created on touch.
- **Smooth scroll: CSS only, no Lenis.** Reasons: Lenis takes over the scroll on the main thread, which costs INP on low-end phones (this repo already removed JS scroll smoothing in commit `62fea2e` because it felt laggy). It also fights `position: sticky` side labels, anchor jumps, `scroll-padding` and the browser's own find-in-page and keyboard scroll. `scroll-behavior: smooth` for in-page anchors is enough, and it's disabled under reduced motion.
- **Small surprises:** the stamp; the terminal answering `sudo` with a joke; the wordmark dot turning vermilion on hover; the 404 terminal.
- **Reduced motion:** every transform animation becomes a 150ms opacity fade; no parallax, marquee, magnetic pull, cursor ring or stamp spin.
- **Performance rules:** animate only `transform`/`opacity`; no layout reads in scroll handlers; one IntersectionObserver; one `requestAnimationFrame` loop that sleeps when idle; nothing animates before the LCP; no GSAP (WAAPI + CSS cover everything here, saving ~70 KB).

---

## 8. Interactive features

1. **Hero element:** see §6.
2. **Ask Amir:** a textarea plus optional name and email. Stored in a `questions` table; answered in Filament (`QuestionResource`); a toggle `is_public` shows Q&A on the home page and on `/ask` (and `/en/ask`; like `/blog` and `/cv`, paths are the same in both languages).
3. **Suggest something + roadmap:** visitors post an idea (title + optional detail) and upvote without logging in. One vote per idea per voter, where voter = `sha256(ip + app.key)` **and** session; both are checked. Rate limits: 3 ideas per hour per IP, 30 votes per hour. Filament `IdeaResource` with status `new / planned / building / shipped / declined` and an `is_public` toggle (new ideas are hidden until you approve them, to stop spam and abuse). Public roadmap at `/roadmap` with columns by status (horizontal scroll on mobile).
4. **Terminal toy:** commands `help, projects, project <n>, about, now, contact, ask <question>, idea <text>, theme dark|light|system, lang nl|en, jebbari, serverscope, clear, whoami, ls, cd ~`, and a few hidden ones. `ask` and `idea` post to the same endpoints as the forms. It replaces the current ⌘K command palette (⌘K / Ctrl+K focuses the terminal).
5. **Extra experiment [CHOOSE]:**
   - **E1 "Ping" (my recommendation):** your browser pings amirs.life five times (`/up`, `cache: no-store`) and shows your round-trip next to the latest real response times this server measured for Jebbari, ServerScope and the live projects (from the existing `site_checks` table, which already exists and runs every 10 minutes). It draws a tiny live latency bar per site in mono. It ties to the ServerScope story with real numbers and no external calls. Copy: "Jouw ping naar mijn server: 23 ms".
   - **E2 "Build your stack":** drag or tap tools into a tray and it tells you which of your projects used that combination. Fun, but it's mostly invented interaction, with weaker ties to real data.

All forms: CSRF token, server-side validation with nl/en messages, honeypot field, `throttle` per route, a success toast, no third-party scripts.

---

## 9. Accessibility, SEO, performance targets

- WCAG AA: every text pair above is ≥ 4.5:1 (large display ≥ 3:1). Focus ring: 2px `--accent` + 2px offset, always visible on `:focus-visible`. A skip link goes to `#main`. The full nav sheet, terminal and modals are keyboard operable. `lang` is set per page, and every image has an alt (or empty alt when decorative).
- SEO: per-page title and description, canonical, hreflang nl/en/x-default (already there), OG and Twitter tags, generated OG images in the new style (GD + bundled static Bricolage TTF), JSON-LD `Person` (with `worksFor` → `Organization` Jebbari, `sameAs`), `WebSite`, `CreativeWork` per case, `BlogPosting` per note, sitemap including the new pages.
- Performance: Lighthouse mobile ≥ 90, LCP < 2s (the hero is text; no hero image above the fold on mobile), CLS ≈ 0 (fixed aspect ratios, font fallback metrics, deterministic note rotation), JS ≤ 35 KB gzip for the home page (Alpine + our modules), WebP/AVIF with `srcset`, `width`/`height` and `loading="lazy"` below the fold.
