# amirs.life · Design system

Status: **approved direction, revision 2** (2026-11-15).
Decisions so far: palette **A, quieter**; hero **H1, the stamp, refined**; experiment **E1, Ping**; admin panel moves to **/amir**.

Revision 2 changes the mood from "expressive editorial" to **calm and sleek, Apple-like in restraint, but personal**. Removed: marquee strip, oversized numerals, sticky side labels, hard offset shadows, rotated notes, custom cursor ring, magnetic buttons, spring easing. Bricolage Grotesque has been replaced by **Instrument Sans** (see §3 for why).

I did not open jebbari.nl, ServerScope or ServerScope-Info for styling. `design-refs/` does not exist in the repo.

---

## 0. Audit: what looks generic now, and why

| # | What | Where | Why it reads as generic / AI-made |
|---|------|-------|-----------------------------------|
| 1 | Zinc-black page, blue `#6e9cf7` accent, green status dots | `resources/css/app.css` | It's ServerScope's palette, and the default "dev dashboard" look. |
| 2 | Radial blue/green glow behind the page plus a 600px cursor glow | `layouts/layout.blade.php` | Glowing orbs. |
| 3 | `.text-gradient` on the name | hero | Gradient text. |
| 4 | `.spotlight` cards with a cursor-following gradient border | everywhere | Generic SaaS card effect. |
| 5 | `rounded-panel` 18px + border on everything | all sections | One uniform card treatment. |
| 6 | Three or four identical cards in a row (decisions, stack, process steps, testimonials) | home | Identical card rows. |
| 7 | Inter + JetBrains Mono | `app.js` | Default developer pairing, shared with ServerScope. |
| 8 | Fake-browser chrome (three dots, URL pill) around screenshots | ServerScope block, case page | Overused mock-up shell. |
| 9 | Stat band with count-up numbers | `#proof` | Reads as fake stats, even though the numbers are real. |
| 10 | "Understand → Build → Deliver → Monitor" process cards | `#proof` | Agency boilerplate. |
| 11 | Boot screen, canvas dot field, heartbeat canvas | `motion.js` | Hacker cliché that costs main-thread time. |
| 12 | Infinite marquee of tech chips | `#stack` | Filler. |
| 13 | "incident · 404 not found" card | `errors/404` | Dashboard metaphor. |
| 14 | "Developer die oplevert", "Proof, not promises", "Why work with me" | `lang/*` | Job-seeker / agency tone. It also contradicts the new positioning. |
| 15 | Project fallback images: a stock photo of tools, a stock-looking garden, a purple gradient with sparkles | `public/images/projects/` | None of them is a screenshot. |
| 16 | GSAP, Vue and vue-router in `package.json` | | Vue is unused, and GSAP only does things CSS can do. |

Worth keeping (behaviour, not looks): locale routing, `lroute()` / `alternate_url()`, the contact flow, TrackVisit, the Setting store (`now.nl` / `now.en`), OG image generation, sitemap with hreflang, and View Transitions.

---

## 1. Personality

**Calm, sleek, confident. A lot of air, big type, one idea per screen.**

- Restraint like Apple: few elements, big statements, generous whitespace, motion that follows the scroll.
- But it's *yours*: warm off-white instead of clinical white, ink instead of pure black, your own voice in the copy, and one tiny vermilion detail (the stamp, the dot in the wordmark, the focus ring).
- Jebbari is the agency. This is the person: quieter and more personal, with small moments of play (the stamp, the terminal, the ping) that only show up when you go looking.
- Voice: first person, short sentences, no superlatives.

---

## 2. Colour: palette A, quieter

Mostly off-white and ink. Vermilion appears **only on tiny details**: the wordmark dot, the stamp, focus rings, the active-nav indicator, and the selected state in the terminal and ideas. Links are ink with an underline, not vermilion. No gradients anywhere.

### Light (default / hero)

| Token | Hex | Use | Contrast on `--bg` |
|---|---|---|---|
| `--n-0` | `#FFFFFF` | raised surfaces (inputs, sheet, toast in dark) | |
| `--n-50` | `#FCFBF8` | subtle raised | |
| `--n-100` | `#F7F5F1` | **page background** | |
| `--n-200` | `#EFECE6` | fills, row hover, image placeholder | |
| `--n-300` | `#E3DED5` | hairlines | 1.23 (decorative) |
| `--n-400` | `#CFC8BC` | strong lines, input borders | 1.53 |
| `--n-500` | `#8F887C` | icons ≥ 20px, disabled | 3.22 |
| `--n-600` | `#6B655B` | muted text, labels | **5.30** |
| `--n-700` | `#48443D` | secondary text | **8.89** |
| `--n-800` | `#2A2824` | | |
| `--n-900` | `#161512` | **ink: text, primary button** | **16.77** |
| `--accent` | `#E2431E` | non-text details only (dot, stamp, ring, 2px indicators) | 3.81 (UI ≥ 3:1 ✓) |
| `--accent-text` | `#B8320F` | the rare case where accent is text (≤ 1 per screen) | **5.51** |

### Dark (alternative): warm night, designed separately

| Token | Hex | Use | Contrast on `--bg` |
|---|---|---|---|
| `--bg` | `#121110` | page | |
| `--surface` | `#1B1A18` | raised | |
| `--fill` | `#24221F` | hover, placeholders | |
| `--line` | `#2E2B27` | hairlines | 1.34 |
| `--line-strong` | `#45413B` | input borders | |
| `--muted` | `#A39E95` | muted text | **7.08** |
| `--text-2` | `#CFCAC1` | secondary text | **11.56** |
| `--text` | `#F2EFE9` | text | **16.44** |
| `--accent` | `#FF6B3D` | details | **6.67** |

In dark: photos get `filter: brightness(.92)`, the primary button becomes off-white on near-black, and the hairlines are slightly stronger, so sections still read.

### Form states only

| | Light | Dark |
|---|---|---|
| error | `#9E1B32` (7.26) | `#FF8A8A` (8.31) |
| success | `#2E6A3B` (5.95) | `#7FD08F` (10.17) |

Errors always have an icon and text, never colour alone.

### Semantic tokens (components use only these)

`--bg --surface --fill --line --line-strong --text --text-2 --muted --accent --accent-text --on-ink --focus --ok --err --selection`

---

## 3. Typography

**Instrument Sans Variable** (display + body) + **Martian Mono Variable** (tiny labels, terminal), self-hosted, latin subset, `font-display: swap`, Instrument Sans preloaded.

Why not Bricolage any more: I rendered the hero line at 96px. Bricolage automatically switches to its display optical size there, which is narrow and quirky, with visible ink traps. Pinning `opsz` to 32 and `wdth` to 100 calms it down, but the `k` and the joins still draw attention. That's the opposite of calm. Instrument Sans is clean, even and confident at 120px, keeps a little warmth (the `g`, the `S`) and works at 17px body size, so one family does both jobs. I compared it with Schibsted Grotesk (an editorial slab-ish `I`, too newspaper) and Hanken Grotesk (calm, but generic).

**Please confirm Jebbari doesn't use Instrument Sans.** I deliberately didn't check.

- No width axis used (`font-stretch: 100%` always). Weights: 400 body, 500 UI, 600 headings.
- Martian Mono is used sparingly: 12–13px labels and the terminal only, `wdth 87`.

### Fluid scale (390 → 1440px viewport)

| Token | Size | Line height | Tracking | Weight |
|---|---|---|---|---|
| `--t-hero` | `clamp(3.5rem, 2.1rem + 5.9vw, 7.5rem)` → **56–120** | 1.0 | -0.03em | 600 |
| `--t-statement` (big centered section statements) | `clamp(2.25rem, 1.4rem + 3.5vw, 4.5rem)` → 36–72 | 1.04 | -0.025em | 600 |
| `--t-title` (project names, case titles) | `clamp(1.75rem, 1.25rem + 2vw, 3rem)` → 28–48 | 1.08 | -0.02em | 600 |
| `--t-h3` | `clamp(1.25rem, 1.15rem + .45vw, 1.5rem)` → 20–24 | 1.2 | -0.01em | 600 |
| `--t-lead` | `clamp(1.1875rem, 1.1rem + .4vw, 1.4375rem)` → 19–23 | 1.45 | -0.005em | 400 |
| `--t-body` | `clamp(1.0625rem, 1.03rem + .12vw, 1.125rem)` → **17–18** | **1.6** | 0 | 400 |
| `--t-small` | 0.9375rem → 15 | 1.5 | 0 | 400 |
| `--t-label` | `clamp(.75rem, .73rem + .08vw, .8125rem)` → **12–13** | 1.3 | **+0.1em**, uppercase | Martian Mono 450 |

`text-wrap: balance` on headings and statements, `pretty` on paragraphs, `tabular-nums` on times and years. Body max width `64ch`.

---

## 4. Layout language

- **One idea per screen.** Each home section opens with one big centered statement (`--t-statement`, max 14 words), then at most one supporting element (an image, a list or a form).
- **Full-width sections**, separated by whitespace, not boxes. No section background colours except one: the Playground section sits on `--n-200` / `--fill` so it reads as a separate "room".
- **Alignment:** statements and the hero are **centered**; reading content (lists, case text, forms) is **left-aligned** inside a centered column. Never centred body paragraphs longer than 2 lines.
- **Grid:** 12 columns, content max **1200px**, reading column max **720px**, outer gutter 20px (mobile) → 32px (≥768) → 48px (≥1280), gap 24px.
- **Whitespace:** section padding `clamp(120px, 8rem + 6vw, 220px)` top and bottom. Inside a section: statement → content gap `clamp(48px, 3rem + 3vw, 96px)`.
- **Spacing scale (4px base):** 4, 8, 12, 16, 24, 32, 48, 64, 96, 128, 192.
- **Radius (3 values):** `--r-0: 0` (sections, rows), `--r-1: 12px` (images, screenshots, inputs, toasts, sheet), `--r-pill: 999px` (buttons, tags, toggles). Tailwind's default radius and shadow scales are removed from the config.
- **Elevation:** no shadows, apart from one very soft shadow for floating layers only (toast, sheet, nav when compact): `0 1px 2px rgb(22 21 18 / .04), 0 8px 24px rgb(22 21 18 / .06)`. Everything else uses hairlines or whitespace.
- **Images:** large, full-bleed or column-wide, `--r-1`, fixed `aspect-ratio`, no frames or fake browser chrome.
- **Z-index:** nav 50, sheet 70, toast 80, skip link 90.

---

## 5. Components (designed once, reused)

| Component | Spec |
|---|---|
| **Nav** | 64px, wordmark `amir·` left (Instrument Sans 600, the dot in accent), 4 links in body 15px/500 (Werk, Over, Speeltuin, Contact), theme + language on the right. After 24px of scroll it gets `--bg` at 85% + a hairline + the soft shadow; **no blur**. On scroll down it slides out of view; on scroll up it comes back (200ms). **Mobile:** a "Menu" text button opens a full-screen sheet on `--bg` with links at `--t-statement` size, staggered fade-in, focus trap, Esc to close. Active link: a 2px accent dot under it. |
| **Buttons** | *Primary:* ink pill, off-white text, 48px tall, 0 24px, 15px/500. Hover: background lightens to `--n-800` (200ms). *Secondary:* pill, 1px `--line-strong`, ink text; hover fills `--fill`. *Text link:* underline 1px at 0.2em offset, colour `--line-strong`; on hover it thickens and darkens to ink (200ms), so nothing moves. Arrow links (`Bekijk de case →`): the arrow moves 3px on hover. No magnetic effect and no lift. |
| **Tag** | 13px/500, pill, 1px `--line`, `--muted` text, 2px 10px. No fill. |
| **Status line** | One quiet line under the hero, 15px muted: `Amsterdam 14:32 · Nu bezig met: …`. A small accent dot marks it as live. The time is server-rendered, then updated each minute. |
| **Section opener** | Small mono label (optional) + centered `--t-statement` + optional lead (`--t-lead`, muted, max 2 lines). |
| **Work chapter** (home, featured projects) | A full-width, scroll-pinned block: the project name at `--t-statement`, centered, with the one-liner and year; then a large screenshot that scales from 0.92 → 1 and fades in as you scroll; then "Bekijk de case →". At most 3 on the home page. |
| **Project row** (more work, `/work`) | A ruled list: `year` · **name** (`--t-h3`) · one-liner · tags · `→`. Desktop hover: siblings fade to 40%, and a preview image (280×180, `--r-1`) follows the cursor with eased lag, no rotation. Touch: no preview. The whole row is one link; focus = hover state. |
| **Case-study layout** | Centered title (`--t-hero` × 0.75) + one-liner, a quiet meta row (Year · Role · Stack · Link), then a full-width hero screenshot. Body in the 720px reading column: **Problem**, **What I built**, **Result**, each with a small mono label. Screenshots alternate between full-bleed and column-wide; phone shots get a simple rounded outline. Ends with previous / next as two large halves. |
| **Certificate item** | One ruled row: `2025 · Title · Issuer · ↗`. |
| **Blog row** | `date` · title (`--t-h3`) · reading time. |
| **Form fields** | Label above (15px/500), input 52px, `--surface` bg, 1px `--line-strong`, `--r-1`, 17px text. Focus: border ink + 3px focus ring in accent at 35%. Error: border `--err`, icon + message below, `aria-invalid` + `aria-describedby`. Submit disabled with a spinner while sending. |
| **Toast** | Bottom center, ink pill/rounded (`--r-1`), off-white text, icon + message, soft shadow, 4s, pause on hover, `role="status"` (`alert` for errors), at most 3 stacked. Fades + rises 8px. |
| **Modal / sheet** | `<dialog>`. Desktop: centered, max 560px, `--surface`, `--r-1`, soft shadow. Mobile: bottom sheet. Scrim `rgb(22 21 18 / .35)`, no blur, focus trap, Esc. |
| **Idea row** | A calm ruled list grouped by status (not sticky notes): title, optional detail, status tag, vote button (`chevron-up` + count, pill). Voted state: an accent dot. |
| **Question item** | The question in `--t-h3`, the answer in body, then `— Amir · date` in muted text. Unanswered questions are never shown publicly. |
| **Terminal** | A calm dark panel in both themes (`#161512`, `--r-1`), Martian Mono 14px, prompt `amir@life ~ %` with the `%` in accent. A **visible input** always at the bottom (works with mobile keyboards), plus command chips under it for touch. History ↑/↓, Tab completion, `aria-live="polite"` output. |
| **Footer** | A big centered email link (`--t-statement`), then one quiet row: links · NL/EN · theme · "Gebouwd met Laravel, door mij." · "Ook: Jebbari ↗" · ©. |
| **404 / 500** | A centered statement ("Deze pagina bestaat niet. Nog niet.") + two buttons. The 500 page has inline CSS and no database calls. |
| **Empty / loading / error** | Empty: one centered muted sentence + an action. Loading: hairline skeleton rows with a slow opacity pulse. Error: an inline message + "Probeer opnieuw". |
| **Theme toggle** | An icon button cycling system → light → dark, with a tooltip label. |
| **Language switch** | `NL / EN`, the current one in ink and the other muted. |

**Icons:** Lucide, inlined via `<x-icon name="…" />` from a whitelisted set, `stroke-width: 1.5`, sizes 16 / 20 / 24. No emoji.

---

## 6. Hero

Centered. Statement at `--t-hero` (two or three short lines), the role line under it (`Founder of Jebbari · Zwolle & Amsterdam`, 15px muted), the status line, and the stamp. The hero fills the first viewport (`min-height: 92svh`) with a lot of air.

**The stamp, refined:** a 96px circle placed below the role line, slightly off-center to the right. It's a single 1px ink hairline ring, with `AMIRS.LIFE · ZWOLLE · AMSTERDAM ·` set around it in Martian Mono 9px (muted) and a small accent dot in the middle. That's it.
- Desktop: it rotates **at most ±10°** to face the cursor, with smooth easing (no spring), and the text ring slowly turns (one revolution per 40s) only while the hero is visible.
- Click or tap: it leaves a faint imprint at that spot: the same ring in ink at 12% opacity, rotated randomly, fading out over 2.5s. At most 4 imprints at once.
- Reduced motion: no rotation and no slow spin; the imprint appears and fades without any scale animation.
- It's one SVG with transforms only (no layout, no CLS), ~1 KB of JS, and it's `aria-hidden`. It's keyboard-focusable as a button labelled "Stempel" that stamps in the center of the hero.

---

## 7. Motion: Apple-style, scroll-driven, quiet

| Token | Value |
|---|---|
| `--ease` | `cubic-bezier(0.22, 1, 0.36, 1)` (entrances, scroll scrubs) |
| `--ease-inout` | `cubic-bezier(0.65, 0, 0.35, 1)` (sheet, nav hide) |
| `--d-ui` | 200ms |
| `--d-ui-slow` | 300ms |
| `--d-reveal` | 800ms |
| `--stagger` | 60ms |

No spring and no overshoot anywhere.

**Entrances (once):** opacity 0 → 1, `translateY(24px)` → 0, `scale(.98)` → 1, 800ms `--ease`, 60ms stagger within a group. Triggered by one shared IntersectionObserver at 15% visibility. Content is visible by default; the hidden start state is only applied when JS and motion are available (with a 2.5s failsafe).

**Hero on load:** the lines fade and rise 20px, 900ms, 80ms stagger, in **pure CSS**, so the LCP text starts painting immediately. Role line, status and stamp follow at +250ms.

**Scroll-driven (scrubbed with the scroll, not time-based):**
1. *Hero exit:* as you scroll away, the hero content goes to `scale(.96)` and `opacity .2` over the first 60% of the viewport.
2. *Work chapters (pinned):* each chapter is a 180vh container with a `position: sticky` 100vh stage. Scroll progress drives: the title fades and rises in during 0–25%, the screenshot scales 0.92 → 1 and fades in during 10–55%, holds, then the whole stage fades to 0 during 85–100% as the next chapter arrives. On mobile the container is 140vh with smaller ranges.
3. *About photo:* a gentle 1.06 → 1 scale plus up to 40px parallax while it crosses the viewport.
4. *Statements:* each section statement scrubs from `opacity .15` to 1 as it crosses the lower third of the viewport.

**How:** CSS scroll-driven animations (`animation-timeline: view()` / named `scroll-timeline` on the pinned container) where supported (Chromium and Safari 26+). Elsewhere, a ~1.5 KB fallback: one passive scroll listener + one `requestAnimationFrame` per frame that writes a single `--p` (0–1) custom property on the visible pinned stages only; CSS maps `--p` to transform and opacity. Only `transform` and `opacity` ever animate.

**Smooth scroll:** native, no Lenis. Scroll-scrubbed animations look smooth because the browser runs them off the main thread. Lenis would put scrolling back on the main thread, which hurts INP on phones (this repo already removed JS scroll smoothing in commit `62fea2e` for that reason) and fights `position: sticky` pinning, anchors and find-in-page. `scroll-behavior: smooth` is used only for in-page anchors.

**Hover:** colour and underline transitions, 200ms. The project-row preview follows the cursor with 120ms eased lag. Nothing jumps or bounces.

**Page transitions:** cross-document View Transitions. The old page fades out in 150ms, the new one fades in in 250ms with an 8px rise. Unsupported browsers just navigate.

**No custom cursor** (dropped in revision 2: it's clutter in a calm design). The native cursor is always visible.

**Reduced motion:** all scroll scrubs are off (pinned sections become normal stacked sections), entrances become 200ms opacity fades, the stamp is static, there are no page-transition slides, and `scroll-behavior` is `auto`.

**Budgets:** nothing animates before LCP, no layout reads in scroll handlers, no GSAP, and home-page JS ≤ 30 KB gzip.

---

## 8. Interactive features

1. **Hero stamp:** §6.
2. **Ask Amir:** a textarea + optional name/email → `questions` table → answered in Filament (`QuestionResource`) → `is_public` toggle shows Q&A on home and on `/ask`.
3. **Suggest something + roadmap:** visitors post an idea and upvote without logging in. One vote per idea per voter (`sha256(ip + app.key)` and session both checked); 3 ideas/hour and 30 votes/hour per IP. Filament `IdeaResource` with status `new / planned / building / shipped / declined` and `is_public` (new ideas stay hidden until approved). The `/roadmap` page lists ideas grouped by status.
4. **Terminal toy:** `help, projects, project <n>, about, now, contact, ask <q>, idea <text>, theme dark|light|system, lang nl|en, jebbari, serverscope, ping, clear, whoami, ls, cd ~`, plus a few hidden ones. ⌘K / Ctrl+K focuses it (it replaces the old palette).
5. **Ping (E1):** your browser pings amirs.life five times (`/up`, no-store) and shows your round-trip next to the latest real response times measured by this server for Jebbari, ServerScope and the live projects (the existing `site_checks` table, every 10 min). Shown as calm horizontal bars with mono numbers. No external calls.

All forms: CSRF, server-side validation with nl/en messages, honeypot, `throttle`, success toast, no third-party scripts.

---

## 9. Accessibility, SEO, performance

- WCAG AA: every text pair listed is ≥ 4.5:1; the accent is used only for UI details at ≥ 3:1. Focus ring: 2px accent + 2px offset on `:focus-visible`. Skip link, full keyboard support (nav sheet, terminal, dialogs, stamp button), semantic landmarks, alt text everywhere.
- SEO: titles/descriptions, canonical, hreflang nl/en/x-default, OG + Twitter, generated OG images in the new style (GD + a static Instrument Sans TTF), JSON-LD `Person` (`worksFor` → `Organization` Jebbari), `WebSite`, `CreativeWork`, `BlogPosting`, sitemap including the new pages.
- Performance: Lighthouse mobile ≥ 90, LCP < 2s (text hero), CLS ≈ 0 (aspect ratios, font fallback metrics via `size-adjust`), AVIF/WebP with `srcset` + `width`/`height`, lazy-loading below the fold.
