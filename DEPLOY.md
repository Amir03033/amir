# Deploying the amirs.life redesign (Plesk Git)

Branch: `claude/amirs-life-redesign-mne6mv`. The Vite build is committed (`public/build`), so the server needs **no Node**.
Nothing in this release drops or renames a table, a column or a route.

---

## 0. Before you deploy

1. **Back up the database** (Plesk → Databases → Export dump). Every migration is additive, but take a backup anyway.
2. Check `.env` on the server:
   - `APP_ENV=production` (**required**: placeholders are only hidden in production) and `APP_DEBUG=false`.
   - `APP_URL=https://amirs.life` (used in the sitemap, canonical URLs and OG images).
   - Nothing new is required. Optional: `SITE_GITHUB` (your GitHub URL; it's added to the JSON-LD `sameAs`).
   - Optional: `SEO_CANONICAL_URL` (default `https://amirs.life`). In production every generated URL uses it.
   - `SITE_EMAIL` is **no longer read**. The public address is fixed to `info@jebbari.nl` in `config/site.php`, so a personal address can't leak back in. `ADMIN_EMAIL` (used by `canAccessPanel()`) is unchanged.
3. Note that contact-form notifications now go to **info@jebbari.nl**, because `site.email` is used for both. Make sure that mailbox exists and receives mail.

## 1. Pull the code

In Plesk → Git, deploy the branch (or merge it into the deployed branch yourself). Then, over SSH:

```bash
cd /var/www/vhosts/amirs.life/httpdocs
composer install --no-dev --optimize-autoloader     # no new packages, but refreshes the autoloader (new classes)
```

## 2. Migrations: run them in this order

Check what is pending first:

```bash
php artisan migrate:status
```

Then run the six new migrations, one by one:

| # | Command | What it does | Additive? |
|---|---|---|---|
| 1 | `php artisan migrate --path=database/migrations/2026_11_16_000001_ensure_featured_and_sort_order_on_projects.php --force` | Adds `projects.featured` / `projects.sort_order` **only if they are missing**; a no-op otherwise. | Yes (`down()` is empty on purpose) |
| 2 | `php artisan migrate --path=database/migrations/2026_11_16_000002_add_profile_fields_to_projects_table.php --force` | Adds `projects.slug` (unique, nullable), `year`, `role`, `summary`, and fills a slug for every existing project from its Dutch title. | Yes |
| 3 | `php artisan migrate --path=database/migrations/2026_11_16_000003_create_questions_table.php --force` | New table `questions` (Ask Amir). | Yes (new table) |
| 4 | `php artisan migrate --path=database/migrations/2026_11_16_000004_create_ideas_table.php --force` | New tables `ideas` and `idea_votes` (suggestions + roadmap). | Yes (new tables) |
| 5 | `php artisan migrate --path=database/migrations/2026_11_16_000005_add_jebbari_url_to_projects_table.php --force` | Adds `projects.jebbari_url` (nullable JSON, nl/en link to the project page on jebbari.nl). | Yes |
| 6 | `php artisan migrate --path=database/migrations/2026_11_16_000006_add_publishing_and_review_fields.php --force` | Adds `projects.is_published` (default **true**, so every existing project stays online), `status`, `lessons`, `seo_title`, `seo_description`, `image_alt`, `gallery_alts`, and `blogs.excerpt`. Each column only if it is missing. | Yes |

If `migrate:status` shows **older** migrations as pending too (for example `2026_11_07_100100_create_events_table`), run plain `php artisan migrate --force` instead. It runs everything pending in date order, including the six above.

```bash
php artisan migrate:status    # all six should now say "Ran"
```

**Review drafts (after migration 6):** seed the three case studies (ServerScope, BarberCuts, Jebbari.nl) and three blog posts as **unpublished drafts**, with their screenshots. Nobody but you (logged in to `/amir`) can see them until you publish each one. See REVIEW.md.

```bash
php artisan db:seed --class=ContentDraftsSeeder --force
```

It is safe to run again: an existing project or post with the same slug (published, or a draft you are editing) is skipped and never changed. To replace an existing one with the seeded draft, run it with `CONTENT_SEED_OVERWRITE=1 php artisan db:seed --class=ContentDraftsSeeder --force`. **Careful:** a published project replaced this way goes offline until you publish it again.

The screenshots are copied to `storage/app/public/projects/<slug>/`, so run the `chown` in step 3 after seeding.

*Superseded:* `ServerScopeProjectSeeder` (the short ServerScope project) is no longer needed. If you already ran it, `ContentDraftsSeeder` skips ServerScope. Keep your page, or use `CONTENT_SEED_OVERWRITE=1` once to get the full case study as a draft.

## 3. Caches, then ownership (always last)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R amirs.life_jt630rcz25r:psacln /var/www/vhosts/amirs.life/httpdocs
```

All three caches were tested on this branch: `route:cache` handles the closure route `/ping`.

## 4. Queue and cron

- **No queue worker is needed.** Mail (contact form) is sent synchronously, and nothing in this release is queued. If you already run a worker, leave it as it is.
- **The scheduler cron must run** (it probably already does, for `site:check` and the visit rollup):
  `* * * * * cd /var/www/vhosts/amirs.life/httpdocs && php artisan schedule:run >> /dev/null 2>&1`
  The Ping section and the uptime bars on the case pages use the `site:check` results (every 10 minutes). **New in this release:** `site:check` now also requests `https://jebbari.nl` every 10 minutes.

## 5. Post-deploy checklist

- [ ] `https://amirs.life/amir` shows the Filament login, and logging in with 2FA works.
- [ ] `https://amirs.life/admin` returns **404**.
- [ ] Home page loads in light and dark, with no `[PLACEHOLDER: …]` boxes visible (that means `APP_ENV=production`).
- [ ] An old project link `/project/1` redirects (301) to `/project/<slug>`.
- [ ] Contact form: send a test message. You get a success toast, the message is in Filament → Inbox → Contactberichten, and a mail arrives at info@jebbari.nl.
- [ ] Ask Amir: send a test question. It appears in Filament → Inbox → Vragen (with a badge). Answer it and toggle "Tonen op de site", and it shows on `/ask`.
- [ ] Ideas: send a test idea. Approve it in Filament → Ideeën & roadmap → "Op de roadmap", and it shows on `/roadmap`. Voting twice counts once.
- [ ] TrackVisit still logs: open a public page in a private window, and a new row appears in Filament → Visits. Visiting `/amir` or `/ping` adds **no** row.
- [ ] Cron: after 10–20 minutes, the Ping section on the home page shows fresh "x minutes ago" measurements, including Jebbari.
- [ ] `https://amirs.life/sitemap.xml` lists `/work`, `/ask`, `/roadmap` and `/certificates` in nl and en.
- [ ] Share a project URL in a link preview (or use opengraph.xyz) and check that the new OG image appears.
- [ ] `/_styleguide` returns 404 in production (it only exists when `APP_ENV=local`).
- [ ] `/werk-met-mij` and `/en/work-with-me` load. The "Werk met mij" button opens `jebbari.nl/nl/aanvraag/stap/1?utm_source=amirs.life…`, and the click shows up in the `/amir` dashboard widget.
- [ ] After `ContentDraftsSeeder`, in a private window: `/project/barbercuts`, `/project/jebbari` and `/blog/how-i-hardened-my-vps-with-fail2ban` return **404**, and none of them is in `/sitemap.xml`. Logged in to `/amir`, the **Voorbeeld** button shows them with a "Concept" banner.

### SEO

- In Plesk → Hosting Settings: tick **"Permanent SEO-safe 301 redirect from HTTP to HTTPS"** and set the **preferred domain to amirs.life**.
- HSTS and optional nginx caching lines: see SEO.md §5.
- Then work through the **SEO post-deploy checklist in SEO.md §8** (Search Console, Bing, Rich Results, schema validator, hreflang, OG preview, PageSpeed), and make the jebbari.nl changes in **SEO-JEBBARI.md**.
- [ ] `curl -sI https://amirs.life/amir/login | grep -i x-robots` prints `noindex, follow`, and `/admin` returns 404.

## 6. Rollback

**Code:** deploy the previous commit in Plesk Git (`ba3abf6` = old `main`), then run the cache commands and the `chown` again. The new tables and columns don't bother the old code, so **you can leave the database as it is**. That's the recommended rollback.

**Database (only if you really want to):** run this on a backup-verified database. It **deletes all questions, ideas and votes** and the new project fields (slug/year/role/summary/jebbari_url, the publishing and SEO fields) and `blogs.excerpt`:

```bash
php artisan migrate:rollback --step=6 --force
```

It was tested on a copy of the database: it rolls back cleanly and migrates again cleanly. Migration 1 never removes `featured`/`sort_order`.

## 7. Content still needed (shown as placeholders until filled)

See the list at the end of the build summary. In short: hero copy approval, about text with your study, the year Jebbari started, project years/roles/one-liners/case text, and real screenshots.
