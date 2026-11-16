# SEO on amirs.life

Goal: search engines and AI assistants understand amirs.life and jebbari.nl as **one person (Amir Jebbari) and one company (Jebbari)**. The visual design is unchanged by this work.

Everything below is tested in `tests/Feature/SeoTest.php` (JSON-LD validity and links, titles and descriptions, headings, alt texts, canonical, hreflang, OG, sitemap, robots, noindex, the www redirect, llms.txt, bots, and "no education/age/internship" text).

---

## 1. Single source of truth: `config/seo.php`

| Fact | Value |
|---|---|
| Name | **Amir Jebbari** (as on jebbari.nl/nl/over-mij) |
| Role | NL "Oprichter van Jebbari" · EN "Founder of Jebbari" (jobTitle: Oprichter / Founder) |
| Canonical sentence | NL **"Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle."** · EN **"Amir Jebbari is the founder of Jebbari, based in Amsterdam and Zwolle."**. These are word for word the sentences jebbari.nl already uses in its Person description and llms.txt. |
| Locations | Amsterdam, Zwolle (NL), always in that order |
| Email | info@jebbari.nl |
| Company | Jebbari, https://jebbari.nl, `@id` **https://jebbari.nl/#org** (the ID jebbari.nl uses) |
| Person `@id` | **https://amirs.life/#amir** |
| sameAs | LinkedIn `in/amir-j-a3948b3a9` (same as jebbari.nl), jebbari.nl/nl/over-mij, jebbari.nl/en/about, `https://jebbari.nl/#amir`, GitHub (**PLACEHOLDER**: set `SITE_GITHUB`) |
| knowsAbout | Web application development, Laravel, PHP, Filament, Livewire, Vue.js, SaaS, Server monitoring |

`config/site.php` reads name, email, location, LinkedIn and the Jebbari URL from `config/seo.php`. The hero sentence (`seo_sentence()`), the CV, JSON-LD, OG images and llms.txt all render from it. Change a fact there and it changes everywhere.

Nowhere on the site (meta, JSON-LD, copy, CV, PDF, OG images, llms.txt) is there any mention of education, age or availability for internships. A test fails if one comes back.

## 2. Structured data map

Every page outputs **one** `application/ld+json` block with a connected `@graph`, built by `App\Support\Seo::graph()`:

```
Person  https://amirs.life/#amir ──worksFor──▶ Organization https://jebbari.nl/#org
   ▲  sameAs: LinkedIn, jebbari.nl/nl/over-mij, jebbari.nl/#amir      │
   └────────────────────────── founder ◀──────────────────────────────┘
WebSite https://amirs.life/#website  (publisher/about → Person)
WebPage | ProfilePage | CollectionPage  {url}#webpage
   isPartOf → WebSite · about → Person · breadcrumb → {url}#breadcrumb · mainEntity → (below)
BreadcrumbList {url}#breadcrumb   (inner pages)
CreativeWork  {url}#project        (case studies; producer → Jebbari when built with Jebbari)
BlogPosting   {url}#article        (notes)
ItemList      {url}#list           (work, notes)
```

| Page | Page type | Main entity / extra nodes |
|---|---|---|
| `/`, `/en` | ProfilePage | Person (`og:type=profile`) |
| `/work` | CollectionPage | ItemList of projects |
| `/project/{slug}` | WebPage | CreativeWork: author/creator = Person, url, description, image, dateCreated (year), keywords, sameAs (live site, Jebbari page), producer = Jebbari |
| `/blog` | CollectionPage | ItemList of notes |
| `/blog/{slug}` | WebPage | BlogPosting: headline, description, datePublished, dateModified, image, author/publisher = Person (`og:type=article` + `article:*`) |
| `/certificates`, `/ask`, `/roadmap` | CollectionPage | |
| `/cv` | ProfilePage | Person |
| `/privacy` | WebPage | |
| `/status`, `/login`, 2FA, 404, `/_styleguide` | **noindex**, no JSON-LD | |

**Why `founder` sits on the Organization:** schema.org has no "Person → founded" property. The link goes Person `worksFor` Organization, and Organization `founder` Person. Both point at the same IDs, so the graph closes.

**Validation:** `SeoTest` checks that every page has exactly one JSON-LD block, valid JSON, `@context` https://schema.org, only known schema.org types, unique `@id`s, that every `{"@id": …}` reference resolves inside the graph, consecutive breadcrumb positions with absolute URLs, and the required Person/CreativeWork/BlogPosting fields. After deploying, also run Google's Rich Results Test and validator.schema.org (checklist below).

## 3. On-page

- **Titles ≤ 60, descriptions ≤ 155**, unique per page and language: `lang/{nl,en}/seo.php`. Projects and notes get generated ones (`Seo::projectTitle()`, `Seo::noteTitle()` / `noteDescription()`), shortened at a word boundary when needed.
- Home and CV carry the "founder of Jebbari, Amsterdam and Zwolle" signal in title, description, OG and the visible hero sentence.
- **Exactly one `<h1>` per page, no skipped heading levels, an `alt` on every `<img>`.** All tested.
- **Canonical**: `Seo::canonical()`. Path only, no query string, no trailing slash. Omitted on noindex pages.
- **hreflang**: nl, en and x-default (= nl), reciprocal, only on pages that exist in both languages. Fixed: `Route::view` parameters no longer leak into hreflang URLs.
- **Open Graph and Twitter** on every page: title, description, url, `og:locale` + `og:locale:alternate`, a 1200×630 image with `og:image:alt`.
- **OG images** are generated in the site's style: `/og/page/{key}.png` (static pages), `/og/project/{id}.png`, `/og/blog/{id}.png`. Cached in `storage/app/public/og` and versioned on content changes. An image uploaded in Filament ("Deelafbeelding") overrides the home page image.

## 4. URL map

| URL (nl) | URL (en) | Indexed | In sitemap |
|---|---|---|---|
| `/` | `/en` | yes | yes |
| `/work` | `/en/work` | yes | yes |
| `/project/{slug}` | `/en/project/{slug}` | yes | yes |
| `/project/{id}` (old) | `/en/project/{id}` (old) | **301 → slug** | no |
| `/blog`, `/blog/{slug}` | `/en/blog`, `/en/blog/{slug}` | yes | yes |
| `/certificates`, `/ask`, `/roadmap` | `/en/…` | yes | yes |
| `/cv` | `/en/cv` | yes | yes |
| `/privacy` | `/en/privacy` | yes | yes |
| `/status` | `/en/status` | **noindex** (live monitoring data, changes every 10 minutes, no lasting value in search) | no |
| `/cv.pdf` | `/en/cv.pdf` | yes (linked) | no |
| `/amir…` (Filament) | | **noindex** (header) | no |
| `/login`, `/two-factor-check` | | **noindex** | no |
| `/ping` | | noindex + `Disallow` | no |
| `/llms.txt`, `/llms-full.txt` | | plain text for AI assistants | no |
| `/sitemap.xml`, `/robots.txt` | | | |
| `www.amirs.life/*` | | **301 → https://amirs.life/*** | |

Old ID URLs are the only redirects; no other URL changed.

## 5. Technical

- **sitemap.xml**: generated on request (always current). Canonical URLs only, nl and en entries, each with nl/en/x-default alternates, and `lastmod` from real `updated_at` values (projects, published notes, certificates, public Q&A, public roadmap). A test fetches every URL in it and expects a 200.
- **robots.txt**: `Allow: /`, `Disallow: /ping`, and the Sitemap line. Private pages are deliberately not disallowed, so crawlers can see their `noindex`. Fixed: an earlier `Disallow: /e` blocked every `/en…` URL; a test now checks each sitemap URL against robots.txt.
- **llms.txt / llms-full.txt**: generated from `config/seo.php` and the database (projects, notes, facts, related sites) with the same canonical sentences as jebbari.nl.
- **Headers** (`App\Http\Middleware\SeoHeaders`, global, so the Filament panel is covered too):
  - `X-Robots-Tag: noindex, follow` on the noindex list in `config/seo.php`;
  - `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options` and `Permissions-Policy`;
  - the www → canonical host 301.
- **Canonical host**: in production `URL::forceRootUrl('https://amirs.life')` + https, so canonical, hreflang, sitemap and OG URLs never use www or a preview host.
- **Bots**: TrackVisit lets Googlebot, Bingbot, ClaudeBot etc. through (200) and only leaves them out of the statistics. Tested.
- **Core Web Vitals** (local Lighthouse mobile, measured with compression on): 100/100/100/100, LCP 1.5 s, CLS 0, TBT ≤ 30 ms. The hero font is preloaded with metric-matched fallbacks; below-the-fold images are lazy-loaded; covers and gallery images get explicit `width`/`height` (`image_size()`, cached); the bundled photo is AVIF/WebP with `srcset`.
- **404s are real 404s** (`noindex`, no canonical), also for unknown projects and notes. There are no soft 404s.

### Server settings (Plesk)

1. **Hosting & DNS → Hosting Settings**: tick **"Permanent SEO-safe 301 redirect from HTTP to HTTPS"**, and set **Preferred domain = amirs.life** (redirects www at the server; the app does it too as a safety net).
2. **HSTS** (only once HTTPS works everywhere): turn on HSTS under **SSL/TLS Certificates** ("SSL It!" → HSTS), or add the header in Apache & nginx Settings:
   `Strict-Transport-Security: max-age=31536000; includeSubDomains`. Start with `max-age=300` for a day if you want to be careful.
3. **Compression and caching**: `public/.htaccess` already sets gzip and `Cache-Control` for `/build/assets`, `/fonts` (1 year, immutable), `/images` and `/storage` (30 days) when Apache serves the files. If nginx serves static files directly (Apache & nginx Settings → "Serve static files directly by nginx"), add this under **Additional nginx directives**, then reload and check that a page and `/build/assets/*.css` still load:

```nginx
gzip on;
gzip_comp_level 5;
gzip_types text/css application/javascript application/json application/xml image/svg+xml text/plain;

location ~* ^/(build/assets|fonts)/ {
    add_header Cache-Control "public, max-age=31536000, immutable";
    access_log off;
}
location ~* ^/(images|storage)/ {
    add_header Cache-Control "public, max-age=2592000";
}
```

## 6. Adding a page correctly

1. Add the route inside the `$pages` group in `routes/web.php`, so it exists at `/x` and `/en/x` and gets hreflang automatically.
2. Add `title` (≤ 60) and `description` (≤ 155) under `pages.<key>` in **both** `lang/nl/seo.php` and `lang/en/seo.php`, plus a `breadcrumbs.<key>` label.
3. In the view: `@extends('layouts.layout', [...])` with `title`, `description`, `crumbs`, an optional `pageType` (`CollectionPage`, `ProfilePage`…), `schema` (extra nodes via `App\Support\Seo`), and `ogImage` (add the key to `OgImageController::PAGES` and `seo.og.kicker` for a generated image).
4. Exactly one `<h1>`, then h2/h3 in order, and an `alt` on every image.
5. If it should be indexed, add it to the sitemap list in `PortfolioController::sitemap()` with a real `lastmod` source. If not, add its path to `noindex` in `config/seo.php`.
6. Add the URL to `SeoTest::pages()`. The existing tests then cover title length, JSON-LD, headings, canonical and hreflang for it.

## 7. Content that still hurts indexing

`<x-ph>` placeholders are **hidden in production**, so no `[PLACEHOLDER]` text is ever indexed. Thin or unapproved content is another matter, so these are worth filling:

1. **Project screenshots**: projects without one show a neutral panel (the name only) and use the generated OG image as `image`. That's fine, but real screenshots help image search and click-through.
2. **Case text** (problem / what I built / result): without it, a case page only shows the description, which is thin.
3. **Project one-liner (`summary`) and role, per language**: if the EN one-liner or role is empty, the Dutch text is shown on the English page (duplicate across the hreflang pair). Description, blog title and content are required in both languages in Filament; one-liner and role are not.
4. **Project year and role**: hidden on the page when empty; `dateCreated` is then missing from the CreativeWork.
5. **DRAFT copy** awaiting your approval (indexable once deployed): the hero lines, "Oprichter. Bouwer." / "Founder. Builder.", "Dingen die ik bouwde.", and the about text (`lang/*/site.php`).
6. **"Jebbari sinds" fact**: hidden in production until you give the year.
7. **GitHub URL** for `sameAs` (`SITE_GITHUB`).
8. **Certificates and notes**: listing pages with no entries show an empty state. They stay indexable but are thin until there is content.

## 8. Post-deploy checklist

- [ ] **Google Search Console**: add a *Domain* property for `amirs.life` (DNS TXT verification), submit `https://amirs.life/sitemap.xml`, and request indexing for `https://amirs.life/` and `https://amirs.life/en`.
- [ ] **Bing Webmaster Tools**: import from Search Console (or verify), and submit the sitemap.
- [ ] **Rich Results Test** (search.google.com/test/rich-results) on `/` and one project page: no errors, and Person / BreadcrumbList detected.
- [ ] **validator.schema.org** on `/`, one project page and one note: no errors. Check that the Person `@id` is `https://amirs.life/#amir` and `worksFor` is `https://jebbari.nl/#org`.
- [ ] **hreflang**: open the source of `/work` and `/en/work`; both list the same nl/en pair, with x-default = nl. Search Console → International targeting shows no errors after a few days.
- [ ] **OG preview**: paste the home page and a project URL into opengraph.xyz (or LinkedIn Post Inspector) and check the 1200×630 image, title and description.
- [ ] **PageSpeed Insights** on mobile for `/` and a project page: aim for 90+ with green LCP/CLS/INP.
- [ ] `curl -sI https://amirs.life/amir/login | grep -i x-robots` prints `noindex, follow`.
- [ ] `curl -sI https://amirs.life/admin` returns **404**.
- [ ] `curl -sI https://www.amirs.life/work` returns **301** to `https://amirs.life/work`, and `http://amirs.life` returns 301 to https.
- [ ] `https://amirs.life/robots.txt`, `/sitemap.xml` and `/llms.txt` load, and the sitemap has no `/status` or `/project/<number>` URLs.
- [ ] Do the SEO-JEBBARI.md changes on jebbari.nl, then re-run the schema validator there.
