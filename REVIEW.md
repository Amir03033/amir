# Review before publishing

Four case studies (ServerScope, BarberCuts, Jebbari.nl, Irooni), three blog posts and four pilot reviews are seeded as **unpublished drafts** in Dutch and English. Guests, search engines, the sitemap, `llms.txt`, the OG images and every list on the site cannot see them. Only you can, when you are logged in to `/amir` with 2FA.

Every detail I could not confirm from the repositories or the live sites carries a visible `[VERIFY: …]` marker. Each one is listed below. Publishing is blocked while any marker is left (in any language, in any field), so you cannot forget one.

Seed them on the server with:

```sh
php artisan db:seed --class=ContentDraftsSeeder --force
```

This is safe to run twice. A project or post that already exists with the same slug, published or a draft you are editing, is skipped. `CONTENT_SEED_OVERWRITE=1` replaces it with the seeded draft, which also takes a published project offline. See DEPLOY.md.

> **Note on ServerScope.** If the ServerScope project already exists on the live site (from `ServerScopeProjectSeeder`), the seeder skips it. You then have two options:
> - keep your current page; or
> - run the seeder once with `CONTENT_SEED_OVERWRITE=1` to get the full case study. It goes offline until you publish it again.

---

## How to publish one item

1. **Open the item.** In `/amir`, go to **Projecten** or **Blog**. The **[VERIFY]** column shows how many markers are left.
2. **Preview it.** Click **Voorbeeld**. The page opens as visitors will see it, with a "Concept" banner and every marker highlighted in yellow.
3. **Edit and remove the markers.** Edit the record and fix or delete each `[VERIFY: …]`.
   - Do this for both languages. Use the NL/EN fields per section.
   - Check the SEO fields too: "Publicatie & SEO" for projects, the SEO fields for posts.
4. **Check the fields without markers.** These are listed under "Numbers and fields without markers" below.
5. **Publish.**
   - Projects: click **Publiceer** in the list, or switch on *Gepubliceerd* in the form.
   - Posts: click **Publiceer**, or set the status to *Gepubliceerd*.
   - If a marker is left, you get "Nog niet te publiceren" with the exact field and language.
6. **Check the live page.** Open it logged out (or in a private window). The project or post now appears in the lists, the sitemap and `llms.txt`.

To leave a sentence out instead of answering it, just delete the sentence together with its marker.

---

## Every [VERIFY] marker

Field names are the Filament form fields. Each marker appears once in NL and once in EN. Answer both.

### Project: ServerScope (`/project/serverscope`), 6 markers

| Field | What to confirm |
|---|---|
| Het probleem (problem) | Is this the reason as you would tell it, and who did you first build it for? |
| Wat ik leerde (lessons) | The third lesson, "write down what is done", uses the 61 → 680+ tests figure from `docs/AUDIT.md`. Is it right for you as written? |
| Het resultaat (result) | Do you want a concrete result here, such as the number of teams or servers? Only if you can back it up. |

### Project: BarberCuts (`/project/barbercuts`), 8 markers

| Field | What to confirm |
|---|---|
| Rol (role) | Did you only build it, or are you also the product owner? |
| Het probleem (problem) | Is this the reason? Did you hear it from a barber you know? |
| Wat ik leerde (lessons) | Is "een middag / an afternoon" right for the deploy problem (bare clone)? |
| Het resultaat (result) | The number of shops or bookings, only if you want to share it and can back it up. |

### Project: Jebbari.nl (`/project/jebbari`), 10 markers

| Field | What to confirm |
|---|---|
| Het probleem (problem) | Is this how you would describe the reason? |
| Wat ik bouwde (solution) | Do you want to mention how often the free website check (`/nl/scan`) is used? |
| Wat ik leerde (lessons) | Which term do you use: "digital studio" or "webbureau"? The text points out that the two sites differ. Pick one, and see SEO-JEBBARI.md. |
| Wat ik leerde (lessons) | The last lesson is a suggestion. Is it true for how you built Jebbari.nl? Adjust it or remove it. |
| Het resultaat (result) | A result, such as requests per month, only if you want to share it. |

### Project: Irooni (`/project/irooni`), 8 markers

Facts come from the `irooni` repository (README, PLAN.md, `docs/phase-0` to `phase-10`) and irooni.nl, both read on 16 Nov 2026. The text stays on culture and the build. It describes no political position, and states openly that drafts are written with AI help, as the site itself does.

| Field | What to confirm |
|---|---|
| Rol (role) | Is Irooni your own project, or did you build it for someone else? If it's a client who doesn't want to be named, keep it anonymous. |
| Het probleem (problem) | Is this the reason as you would tell it? |
| Wat ik leerde (lessons) | The photo lesson: 89 of 120 Commons candidates used (from `docs/phase-8` and `phase-10`). Is it right for you as written? |
| Het resultaat (result) | Do you want to mention published articles or readers? Only if you can back it up. "More than 130 tests" comes from the repo (139 test methods). |

### Post: "Hoe ik mijn VPS beveiligde met fail2ban" (`how-i-hardened-my-vps-with-fail2ban`), 14 markers

All are in **content**, in this order:

1. How soon did you see the first login attempts, and roughly how many per day?
2. Do you use Plesk's own Fail2Ban screen (Extra en instellingen → IP-adressen blokkeren) or only the command line?
3. Which `bantime` / `findtime` / `maxretry` values do you use? Is the Plesk panel log path (`/var/log/plesk/panel.log`) right on your server?
4. Replace the example `failregex` with the one you actually use.
5. How do you keep yourself from being banned: a fixed IP, a VPN, or both? The example IP is `203.0.113.10`, a documentation address. Do **not** replace it with your real IP.
6. Which ports were open that shouldn't have been, according to nmap?
7. Roughly how many addresses were on the ban list per day after a few weeks?

### Post: "Bots buiten mijn bezoekersstatistieken houden in Laravel" (`keeping-bots-out-of-my-visitor-stats-in-laravel`), 6 markers

1. Are "about 690 visits, of which about 210 bots/scanners, in three days" right, and when was that?
2. Country lookup: the code in this repository looks it up **directly** in the local DB-IP database, inside the middleware. You mentioned a queued job. Which version runs on your server? Adjust the paragraph.
3. Optional: what share of visits is filtered out now?

The rest of this post matches the code. The `TrackVisit` middleware, the blocklist, the suspicious paths, the hashed and shortened IP, and skipping `/amir` and Livewire are all checked by `tests/Feature/TrackVisitTest.php`. That makes the sentence "I have automated tests for it" true.

### Post: "Wat het bouwen van ServerScope me leerde" (`what-building-serverscope-taught-me`), 8 markers

1. Is this how it started, and when was that?
2. Does the example of the mistake match what happened to you?
3. Optional: a number or experience about the database growing (metrics retention).
4. The last lesson is a suggestion ("let someone use a first version earlier"). Do you recognise it? Adjust it or remove it.

You can list the markers yourself at any time:

```sh
grep -no "\[VERIFY[^]]*\]" database/seeders/content/*.php
```

The **[VERIFY]** column in Filament shows what is left in the database.

---

## Pilot reviews (hidden, text only)

`TestimonialDraftsSeeder` (part of `ContentDraftsSeeder`) adds four **hidden** reviews under **Reviews** in Filament. They have no photos; you add those yourself.

**These are not real quotes.** Each text is a suggestion to send to a real client:
- The client approves it, or rewrites it in their own words.
- Name, role and company are `[VERIFY]` placeholders, because I don't invent people or companies.

| Review | Linked to | What to do |
|---|---|---|
| A ServerScope user | ServerScope | Find a user who wants to be quoted, send them the text, fill in name/role, and leave the company empty if they prefer. |
| A barber | BarberCuts | The same, with a shop that uses BarberCuts. |
| Irooni | Irooni | Only if Irooni was built for someone else. If it's your own project, delete this review. |
| A Jebbari client | (no project) | A general client review for the home page. |

**To publish one:**
1. Edit the review.
2. Replace the name, role and company, or empty the company field.
3. Put in the approved text in NL and EN, and remove the `[VERIFY: …]` at the end.
4. Optionally add a photo.
5. Switch on **Tonen op de site**, or click **Publiceer** in the list.

With a `[VERIFY]` marker left, it refuses with "Nog niet te publiceren". The review section on the home page and on the project page appears automatically as soon as one review is visible.

Running the seeder again adds nothing for a project that already has a review, so it never touches a review you edited.

## Numbers and fields without markers

The publish guard only reads text. Check these fields by hand:

| Item | Field | Seeded value | What to confirm |
|---|---|---|---|
| ServerScope, BarberCuts, Jebbari.nl | Jaar (year) | 2026 | The year you built or launched it. |
| Irooni | Jaar (year) | 2026 | From PLAN.md: approved 13 Nov, live 14 Nov 2026. |
| All four projects | Status | live | That each is still live when you publish. Irooni is public, but the site says "in preparation"; choose *In ontwikkeling* if you prefer. |
| All four projects | Uitgelicht (featured), volgorde (sort_order) | ServerScope, BarberCuts and Jebbari.nl featured (1 / 2 / 3); Irooni not featured (4) | The home page shows at most three featured projects. To put Irooni there, switch another one off. Irooni always appears on `/work`. |
| BarberCuts | Result text | "free during the launch, then €10 a month" | Taken from barbercuts.nl/prijzen on 16 Nov 2026. Check that the price is still the same. |
| ServerScope | Result text | "Free, Pro and Premium" plans | Taken from info.serverscope.nl. |
| All three posts | Publication date | set when you publish | The date shown in the preview is the creation date. It becomes the real date the moment you publish. |
| All three posts | Tags, category | see Filament | Fine to change. |

Facts that **are** sourced:
- ServerScope: README, `agent/README`, `docs/AUDIT.md`, CHANGELOG, and the public demo.
- BarberCuts: the separate `barbercuts` repository (`docs/BOUWOPDRACHT.md`) and barbercuts.nl. Nothing from the JF Cuts project was used.
- Jebbari.nl: the jebbari repository and the live services, process, FAQ, request and scan pages.

---

## Placeholders in the text

These are deliberate and should stay:

| Where | Placeholder | Why |
|---|---|---|
| fail2ban post | `203.0.113.10`, `198.51.100.x`, `127.0.0.1` | Documentation IP ranges (RFC 5737). Never put your own IP here. |
| fail2ban post | `server.example.com` | Placeholder hostname. |
| Bots post | `203.0.113.0` | Example of a shortened IP. |
| ServerScope screenshots | `edge-01`, `db-01`, `blog.example.com`, … | ServerScope's public demo data, not real servers. |

`tests/Feature/ContentDraftsTest.php` fails if any other IP address, any other email address than info@jebbari.nl, a home-directory path or something that looks like a key gets into the seeded text.

---

## Screenshots to approve

All screenshots were taken with Playwright from public pages, at 1440 px (desktop) and 390 px (mobile). They are WebP files with explicit width and height.
- In the repository: `database/seeders/media/case-studies/<slug>/`.
- After seeding: `storage/app/public/projects/<slug>/`.
- Provenance: `database/seeders/media/case-studies/README.md`.

None of them shows a login with credentials, real customers, IPs or tokens. Please check each one anyway:

**ServerScope** (public demo at serverscope.nl/demo, with sample data and a "demo" banner)
- [ ] `demo-dashboard-desktop-light.webp`: cover
- [ ] `demo-dashboard-desktop-dark.webp`
- [ ] `demo-incidents-desktop-light.webp`
- [ ] `demo-dashboard-mobile-light.webp`
- [ ] `demo-dashboard-mobile-dark.webp`
- [ ] `site-home-desktop-light.webp` (info.serverscope.nl)
- [ ] `site-home-mobile-dark.webp`
- [ ] `site-pricing-desktop-light.webp`
- Captured but not used in the gallery: `site-home-desktop-dark`, `site-home-mobile-light`.

**BarberCuts** (barbercuts.nl, light theme only: the site has no dark mode)
- [ ] `home-desktop-light.webp`: cover. **Check:** the phone mock-up shows names such as "Sam" and "Daan de Vries". Confirm they are made-up examples, not real customers. If they are real, I will retake the shot cropped.
- [ ] `home-mobile-light.webp`: same mock-up check.
- [ ] `pricing-desktop-light.webp`, `pricing-mobile-light.webp`
- [ ] `signup-mobile-light.webp`: an empty sign-up form. The footer shows the company's KvK number, which is public on barbercuts.nl. Fine to show? If not, I will crop it.
- The public shop directory (`/kappers`) was deliberately **not** captured: it lists real businesses.

**Irooni** (irooni.nl, light only: each section has its own colours, and there is no theme toggle)
- [ ] `home-en-desktop.webp`: cover. The Isfahan photo is "Amir Pashaei, CC BY-SA 4.0", credited in the image itself.
- [ ] `home-fa-desktop.webp`: the Persian home page, right to left.
- [ ] `article-persepolis-desktop.webp`, `article-persepolis-mobile.webp`: the Persepolis article, including its line about AI assistance.
- [ ] `home-en-mobile.webp`
- Deliberately not captured: `/admin`, and the history timeline (it opens with a portrait of a Shah, which reads as political on a portfolio).

**Jebbari.nl** (dark captures use the site's own theme toggle; the site ignores the system setting)
- [ ] `home-desktop-light.webp`: cover
- [ ] `home-desktop-dark.webp`
- [ ] `services-desktop-light.webp`
- [ ] `request-desktop-light.webp`: an empty form, step 1 of 5
- [ ] `home-mobile-light.webp`, `home-mobile-dark.webp`
- [ ] `request-mobile-light.webp`

**Light and dark screenshots.** ServerScope and Jebbari.nl have a dark version of the cover and of some gallery shots:
- In dark mode visitors see the dark version, in light mode the light one.
- A shot with only one version shows that version in both themes.

In Filament:
- **Cover:** "Afbeelding (licht thema)" and "Afbeelding (donker thema, optioneel)".
- **Gallery:** one row per screenshot under "Galerij", each with a light upload, a dark upload and NL/EN alt texts.

Alt texts are in each "Galerij" row (NL/EN) and in "Publicatie & SEO" for the cover.

---

## "Work with me" and the CTAs

These are live code, not drafts, so they are already visible after deploy:
- `/werk-met-mij` and `/en/work-with-me`;
- the burgundy nav item;
- the blocks on home, in the footer, and at the end of each case study and post.

The services and the four process steps come from jebbari.nl. Check that they match what you offer today. All links carry `utm_source=amirs.life&utm_medium=portfolio&utm_campaign=<placement>`. You can see clicks per placement in the dashboard widget **"Werk met mij"-kliks per plek** on `/amir`.

One thing to fix on jebbari.nl: `/nl/aanvraag` and `/en/request` redirect to step 1 and **drop the query string**, so the UTMs would be lost. For now amirs.life links straight to `/nl/aanvraag/stap/1` and `/en/request/step/1`. I did not change jebbari.nl; see SEO-JEBBARI.md.

---

## Self-check (done)

I scanned all seeded text. The checks are automated in `ContentDraftsTest::test_seeded_text_passes_the_self_check`:

- **Private data:** no real IPs (only RFC 5737), no email addresses other than info@jebbari.nl, no usernames, home paths, tokens or keys.
- **No fabrication:**
  - no testimonials or quotes;
  - no client or company names other than your own products;
  - no awards, certifications or event dates;
  - nothing legal or financial beyond the public price;
  - every number that isn't from a repo or live page is behind a `[VERIFY]`;
  - the 690 / 210 figures come from you and are still marked so you can confirm the period.
- **No sloppy phrases:** none of passionate, cutting-edge, seamless, unleash, game-changer, world-class, next-level, revolutionary, synergy, leverage or delve, in either language.
- **No forbidden positioning:** no education, age or "open to internships". The positioning line is "oprichter van Jebbari, gevestigd in Amsterdam en Zwolle".
- **Length:** posts are 700–1100 words per language, SEO titles are ≤ 60 characters and descriptions ≤ 155–160.
