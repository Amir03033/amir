# amirs.life · Build plan

Status: **approved, revision 2** (2026-11-15). Choices: palette A (quieter), hero H1 stamp (refined), experiment E1 Ping, admin panel to `/amir`. Section A proposals 3–5 go ahead as recommended unless you say otherwise.
Branch: `claude/amirs-life-redesign-mne6mv`, small commits, one PR at the end (only if you ask for one).

---

## A. Things I found that you need to decide first

1. **[DECIDED: move to /amir]** **The admin panel is at `/admin`, not `/amir`.** `bootstrap/providers.php` loads `AdminPanelProvider` (`->path('admin')`). `AmirPanelProvider` (`->path('amir')`) is **commented out**, and it references three widget classes that no longer exist (`VisitsOverview`, `VisitsChart`, `TopReferrers`), so turning it back on would crash. The tests (`AdminPanelTest`) also hit `/admin/...`. Your brief says both "stays at /amir" and "don't touch AdminPanelProvider routing", and today those two contradict each other. Options:
   - **(a) Recommended:** change only `->path('admin')` → `->path('amir')` in `AdminPanelProvider`, update the tests, and delete the dead `AmirPanelProvider`. `canAccessPanel()`, 2FA and middleware stay untouched. TrackVisit already excludes both `admin*` and `amir*`.
   - (b) Leave everything at `/admin` and don't touch it.
2. **Positioning change.** The current copy says "Laravel developer, 18, student in Overijssel, open to internships / junior roles". The brief says "Founder of Jebbari · Zwolle & Amsterdam, builder of ServerScope, student". I'll rewrite the copy for the new positioning, but **I won't invent facts**: anything factual I don't have is a marked placeholder (list in §E).
3. **Existing features not in the brief.** What should happen to them?
   - `/status` page + the home "Live status" dashboard → **proposal:** remove the dashboard from home (it's the ServerScope look); keep `/status` restyled as a quiet page, and feed experiment E1 with the same data.
   - `/cv` + `/cv.pdf` → keep, restyle, linked from about and the footer.
   - ⌘K command palette → merged into the terminal toy.
   - Testimonials → keep the model and resource; the section stays hidden unless real, visible ones exist (none are seeded).
   - Booking link → keep as an optional secondary button in contact if `SITE_BOOKING_URL` is set.
   - Boot intro animation, canvas dot field, heartbeat canvas, cursor glow, spotlight cards → **removed**.
4. **Project URLs.** Case studies live at `/project/{id}`. Adding slugs (`/project/serverscope`) would be nicer for SEO, but it changes URLs. **Proposal:** add a nullable `slug`, resolve by slug *or* id, and 301 the id URLs to slug URLs. Nothing breaks. Say no and I keep ids.
5. **ServerScope.** It's currently a hard-coded section in the lang files, not a `Project` record. **Proposal:** make it a normal project (featured, `sort_order` 1) with a full case study, so all work uses one system. Jebbari itself could also be a row (link out, no case study).
6. **[DECIDED]** Palette A (quieter), hero H1, experiment E1. Fonts changed to Instrument Sans + Martian Mono; please confirm Jebbari doesn't use them.
7. Laravel is **12.71** in `composer.lock` (not 11) and Filament is 3.3. That's no problem; I'm just noting it.
8. `design-refs/` doesn't exist. Push it if you have it.

---

## B. Information architecture

| Path (nl at `/…`, en at `/en/…`) | Page | Status |
|---|---|---|
| `/` | Home | redesigned |
| `/work` | Projects index (all projects as rows, filter by tag) | **new** |
| `/project/{project}` | Case study | redesigned (+ slug, see A4) |
| `/blog`, `/blog/{slug}` | Notes | redesigned |
| `/certificates` | Certificates list | **new** (home shows the top 5) |
| `/roadmap` | Public ideas board by status + suggest form | **new** |
| `/ask` | Answered public questions + ask form | **new** |
| `/now` | Small "now" page (what I'm doing, editable in Filament) | **new, optional** |
| `/cv`, `/cv.pdf`, `/status`, `/privacy` | | restyled, unchanged routes |
| `POST /ask`, `POST /ideas`, `POST /ideas/{idea}/vote` | form endpoints, `throttle` | **new** |
| `POST /contact` | unchanged endpoint, restyled form | unchanged |
| `GET /ping` | E1 only: tiny JSON with latest site_checks per site, cached 60s | **new** |
| `/sitemap.xml`, `/og/{type}/{id}.png` | add the new pages and an OG type for `page` | extended |

No routes are renamed or removed.

**Home section order (rev. 2, one idea per screen):** Hero (centered statement, role line, status line, stamp) → Werk: up to 3 pinned work chapters, then a quiet "more work" row list → Over (centered statement, full-width photo, a few facts, Jebbari link) → Speeltuin on its own tinted band (Ask Amir · Suggest + roadmap teaser · Terminal · Ping) → Certificaten (compact list) → Notities (3 rows) → Contact (big email + form) → footer.

---

## C. Data model changes (all additive)

| Migration | Change | Notes |
|---|---|---|
| `ensure_featured_and_sort_order_on_projects` | Adds `featured` / `sort_order` **only if missing** (`Schema::hasColumn` guards) | Safety for production, where you suspect they're missing. A no-op if they exist. |
| `add_profile_fields_to_projects` | `slug` (nullable, unique), `year` (smallint null), `role` (json null), `summary` (json null, the one-liner), `case_published` (bool, default true) | Year / role / summary feed the rows and case header. |
| `create_questions_table` | `id, name?, email?, body, locale, answer? (text), answered_at?, is_public (bool, default false), voter_hash, user_agent?, timestamps` | |
| `create_ideas_table` | `id, title, body?, status (new/planned/building/shipped/declined), is_public (bool, default false), votes_count (uint), locale, voter_hash, sort_order, timestamps` | |
| `create_idea_votes_table` | `id, idea_id (fk cascade), voter_hash, session_hash, created_at`, unique(`idea_id`, `voter_hash`) | |
| Settings (no migration) | reuse `now.nl` / `now.en` for "currently building"; add `now.page.nl` / `now.page.en` for /now | via `SiteSettings` page |

**Filament:** `QuestionResource` (inbox-style: unanswered filter, answer textarea, public toggle, nav badge with the unanswered count), `IdeaResource` (status select, public toggle, votes column, bulk status change), new fields on `ProjectResource` (slug, year, role, summary), and a "Speeltuin" group in the nav. The panel's look is unchanged except that its primary colour moves from blue to the chosen accent (tell me if you'd rather leave the admin alone).

**Nothing is dropped.** The `testimonials`, `site_checks`, `visits` and `events` tables all stay.

---

## D. Phases

Each phase ends with: a summary, what to test, and Playwright screenshots at **390px and 1440px, light and dark**, saved in `docs/screens/phase-N/` and sent to you.

### Phase 1: Design system + layout shell
- Replace `tailwind.config.js` theme: semantic colour tokens, the three radii, type scale, spacing, no default shadows or radii.
- `resources/css/` split into `tokens.css`, `base.css`, `components.css`, `motion.css`.
- Fonts: remove Inter / JetBrains Mono, add Bricolage Grotesque + Martian Mono (latin), preload, fallback metrics.
- Theme: system default, manual toggle (light / dark / system) remembered in `localStorage`, inline pre-paint script (no flash), `theme-color` meta per theme.
- Fonts: Instrument Sans (display + body) + Martian Mono (labels, terminal), replacing Inter / JetBrains Mono.
- Shell: layout, skip link, nav (hide on scroll down, full-screen mobile sheet), footer, `<x-icon>`, buttons, link, tag, form fields, toast, `<dialog>` sheet, section opener, 404 and 500.
- Motion foundation: reveal observer, scroll-scrub engine (CSS scroll timelines + `--p` fallback), pinned-stage primitive, View Transitions, reduced-motion handling.
- JS: small modules (`theme`, `nav`, `reveal`, `scrub`, `toast`). Remove GSAP, Vue, vue-router, `@vitejs/plugin-vue`.
- Admin: `AdminPanelProvider` path `admin` → `amir`, tests updated, dead `AmirPanelProvider` removed.
- Temporary legacy aliases so the old pages stay readable until Phases 2–3 replace them (deleted in Phase 6).
- Hidden `/_styleguide` page (local env only) that shows every component in both themes, for review.

### Phase 2: Home
- Hero (centered statement, role line, status line with Amsterdam time + "currently building", refined stamp), pinned work chapters + more-work rows with cursor preview, about (photo, facts, Jebbari link), certificates (compact), notes (3 rows), contact (big email + form). JSON-LD Person + Organization.

### Phase 3: Projects + case studies
- `/work` index, case-study layout, prev/next, gallery crops, slug support (if approved), new project fields in Filament, image derivatives (AVIF/WebP + `srcset`) via the existing `OptimizeImages` command, extended.

### Phase 4: Interactive features
- Ask Amir (form, endpoint, public Q&A), ideas + votes + `/roadmap`, terminal toy, experiment (E1/E2), `/now`. Throttle and honeypot on everything; nl/en validation messages; toasts.

### Phase 5: Filament resources
- `QuestionResource`, `IdeaResource`, `ProjectResource` fields, `SiteSettings` additions, dashboard widget "nieuwe vragen / ideeën". The panel path fix depends on your answer to A1.

### Phase 6: Polish, performance, accessibility, SEO
- Lighthouse mobile runs (local, against `php artisan serve` with a production build), axe-core run on every page, keyboard pass, reduced-motion pass, OG images restyled, sitemap, privacy page updated (questions and ideas store a hashed IP), restyle `/cv`, `/status`, `/privacy`, `/blog`.
- **Self-review:** go through the sloppy list (brief §2) item by item, then a written comparison against jebbari.nl and ServerScope-Info. That's the only moment I look at those two sites, and only to confirm we're different. If it feels like a sibling template, I redo it.

Tests: the existing feature tests stay green (updated for markup where needed), plus new tests for questions, ideas, votes (dedupe + throttle), honeypot, slug redirects, sitemap entries, and the admin pages for the new resources.

---

## E. Content I need from you (placeholders until then)

Placeholders render as a visible dashed box with `[PLACEHOLDER: …]` in local and staging, and are hidden in production.

1. **Hero statement** in your voice, nl + en. I'll write two drafts for you to edit; I won't publish them as yours without your OK.
2. **About text** for the new positioning: what you study and where, the year you started Jebbari, Zwolle & Amsterdam, what you do there.
3. **3–5 real facts** for the about list (e.g. year founded, what you're studying, a number you can back up).
4. **Photo:** `amir2.png` / `amir3.png` (Salzburg bridge, 8 MB / 4 MB) are the only real portraits. OK to use one, cropped? A calmer portrait would be better.
5. **Project details** per project: year, your role, a one-line description (nl + en), problem / built / result text.
6. **Real screenshots** for KlusKlaar, Jungle Tuinen and MasterMind. The current fallbacks are stock photos and a purple gradient, which I will **not** use. Without screenshots the rows show no hover preview and the case pages show a "screenshot follows" placeholder.
7. **ServerScope screenshots** (the `ss-*` slots in Filament "Foto's & media"); if they're already uploaded on production, fine.
8. **Jebbari:** the exact public URL and how you want it described in one line.
9. **Email to show:** `config('site.email')` is a gmail address. Should amirs.life show that, or a domain address?
10. **GitHub URL** (`SITE_GITHUB` is empty) and whether LinkedIn stays.
11. **"Currently building"** text (Filament → Links & instellingen → Nu bezig met).
12. Certificates and blog posts come from the database. If production has none, those sections hide themselves.

---

## F. Deploy (Plesk Git)

The build is committed (`public/build`), because Node on the server is limited. After each push to the deployed branch, run on the server, in this order:

```bash
cd /var/www/vhosts/amirs.life/httpdocs
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R amirs.life_jt630rcz25r:psacln /var/www/vhosts/amirs.life/httpdocs   # ALWAYS last
```

Migrations are given separately at the end of the phase that adds them (Phases 3–5), as exact commands, e.g.:

```bash
php artisan migrate --path=database/migrations/2026_11_16_000001_ensure_featured_and_sort_order_on_projects.php --force
```

plus a `php artisan migrate:status` check before and after. Every migration is additive with a working `down()`. Nothing drops or renames a table or column without asking you first.

---

## G. What I will not do without asking

Drop or rename tables or columns, delete content or uploaded files, rename or remove routes, change `canAccessPanel()`, change TrackVisit exclusions or bot filtering, touch the Visit resource and its widgets, push to `main`, or open a PR.
