# What to change on jebbari.nl so both sites describe one person and one company

Nothing here was changed on jebbari.nl from this repo. These are the snippets to add there yourself. Checked against jebbari.nl on 2026-11-16.

## What jebbari.nl has now (good news first)

- Organization: `@type: ProfessionalService`, **`@id: https://jebbari.nl/#org`**, founder → `https://jebbari.nl/#amir`, email info@jebbari.nl, areaServed Zwolle + Amsterdam.
- Person: `@id: https://jebbari.nl/#amir`, name "Amir Jebbari", jobTitle "Oprichter", worksFor → `#org`, `sameAs` already includes `https://amirs.life`.
- Its Person description and llms.txt already use the canonical sentence: "Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle."
- llms.txt already lists amirs.life, and /nl/over-mij already links to amirs.life.

amirs.life now uses `@id https://jebbari.nl/#org` for Jebbari, the same sentence, the same email, and the same LinkedIn profile. It lists `https://jebbari.nl/#amir` and your jebbari.nl about pages in its Person `sameAs`.

## 1. Use one Person ID on both sites (recommended)

There are two IDs for you today: `https://jebbari.nl/#amir` and `https://amirs.life/#amir`. They are linked by `sameAs`, which works, but one ID is clearer. **Recommendation:** let jebbari.nl point at `https://amirs.life/#amir` (your personal site is the natural home of the Person entity) and keep the old ID as a `sameAs`.

Replace the Person block on jebbari.nl with:

```json
{
  "@context": "https://schema.org",
  "@type": "Person",
  "@id": "https://amirs.life/#amir",
  "name": "Amir Jebbari",
  "givenName": "Amir",
  "familyName": "Jebbari",
  "url": "https://amirs.life",
  "email": "mailto:info@jebbari.nl",
  "jobTitle": "Oprichter",
  "description": "Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle.",
  "worksFor": { "@id": "https://jebbari.nl/#org" },
  "address": [
    { "@type": "PostalAddress", "addressLocality": "Amsterdam", "addressCountry": "NL" },
    { "@type": "PostalAddress", "addressLocality": "Zwolle", "addressCountry": "NL" }
  ],
  "knowsAbout": ["Web application development", "Laravel", "PHP", "Filament", "Livewire", "Vue.js", "SaaS", "Server monitoring"],
  "sameAs": [
    "https://amirs.life",
    "https://jebbari.nl/#amir",
    "https://jebbari.nl/nl/over-mij",
    "https://jebbari.nl/en/about",
    "https://www.linkedin.com/in/amir-j-a3948b3a9"
  ]
}
```

On the English pages, use `"jobTitle": "Founder"` and `"description": "Amir Jebbari is the founder of Jebbari, based in Amsterdam and Zwolle."`.

*If you'd rather keep `https://jebbari.nl/#amir` as the ID on jebbari.nl,* keep your current block and just add `"https://amirs.life/#amir"` to its `sameAs`. amirs.life already points back.

## 2. Organization block

Keep the `@id` exactly as it is. Change `founder`, clean up `url` and `sameAs`, and add amirs.life:

```json
{
  "@context": "https://schema.org",
  "@type": "ProfessionalService",
  "@id": "https://jebbari.nl/#org",
  "name": "Jebbari",
  "legalName": "Jebbari",
  "url": "https://jebbari.nl",
  "email": "info@jebbari.nl",
  "founder": { "@id": "https://amirs.life/#amir" },
  "areaServed": [
    { "@type": "City", "name": "Amsterdam" },
    { "@type": "City", "name": "Zwolle" },
    { "@type": "Country", "name": "Netherlands" }
  ],
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Professor W.H. Keesomlaan 12",
    "postalCode": "1183 DJ",
    "addressLocality": "Amstelveen",
    "addressCountry": "NL"
  },
  "sameAs": [
    "https://amirs.life",
    "https://www.linkedin.com/in/amir-j-a3948b3a9"
  ]
}
```

Notes:
- `url`: use `https://jebbari.nl` (stable), not `https://jebbari.nl/nl`.
- `sameAs`: remove the LinkedIn URL with `?utm_source=…`; it's a duplicate of the clean one. `https://amirs.life` is in the list because you asked for it. Strictly speaking, Organization `sameAs` is meant for pages about the company, so if you add a LinkedIn *company* page later, put that here as well.
- If you use the single Person ID from step 1, also change `worksFor`/`founder` references elsewhere on jebbari.nl (WebSite `publisher`, ProfilePage `mainEntity`, BlogPosting `author`) to `https://amirs.life/#amir`.

## 3. About / team mention (visible text)

On `/nl/over-mij`, near the top:

```html
<p>Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle.
   Meer over mij persoonlijk staat op <a href="https://amirs.life" rel="me">amirs.life</a>.</p>
```

On `/en/about`:

```html
<p>Amir Jebbari is the founder of Jebbari, based in Amsterdam and Zwolle.
   More about me personally is on <a href="https://amirs.life/en" rel="me">amirs.life</a>.</p>
```

In the footer (both languages), next to the other links:

```html
<a href="https://amirs.life" rel="me">amirs.life</a>
```

`rel="me"` tells search engines and fediverse tools that both pages belong to the same person. amirs.life links to jebbari.nl with anchor text "Jebbari" in the hero sentence, the about section and the footer.

## 4. Project pages that exist on both sites

ServerScope is on both: `https://jebbari.nl/nl/werk/serverscope` and amirs.life `/project/serverscope`. On the jebbari.nl project page, add:

```html
<p>Meer over hoe ik ServerScope bouwde: <a href="https://amirs.life/project/serverscope">ServerScope op amirs.life</a>.</p>
```

and in its JSON-LD: `"author": { "@id": "https://amirs.life/#amir" }`, `"sameAs": ["https://amirs.life/project/serverscope"]`.

On amirs.life, fill in "Projectpagina op jebbari.nl" in Filament for every project that is also a Jebbari project. The case page then links to Jebbari and to that page, and the JSON-LD sets `producer` = Jebbari.

## 5. llms.txt on jebbari.nl

It already lists amirs.life. Make the line explicit:

```
- [amirs.life](https://amirs.life): personal site of Amir Jebbari, founder of Jebbari (Person @id https://amirs.life/#amir)
```

## 6. Keep these identical on both sites

| | Value |
|---|---|
| Name | Amir Jebbari |
| Role | Oprichter van Jebbari / Founder of Jebbari |
| Sentence | Amir Jebbari is de oprichter van Jebbari, gevestigd in Amsterdam en Zwolle. / … the founder of Jebbari, based in Amsterdam and Zwolle. |
| Cities | Amsterdam en Zwolle (this order) |
| Email | info@jebbari.nl |
| LinkedIn | https://www.linkedin.com/in/amir-j-a3948b3a9 |

**Inconsistencies to decide on** (I didn't change these on either site):
1. **What Jebbari is called.** jebbari.nl's meta description says "een onafhankelijk *digital studio* uit Zwolle en Amsterdam" (and "Zwolle en Amsterdam" in that order). amirs.life calls it "webbureau / web agency" in the footer and about text. Pick one term, and I'll align amirs.life.
2. **Registered address in Amstelveen vs "Amsterdam en Zwolle".** That's fine as a postal address next to `areaServed`, but don't add Amstelveen as a "based in" city anywhere.
3. **Organization description on jebbari.nl** starts with "Ik ben Amir Jebbari, softwaredeveloper en ondernemer…". Consider using the canonical sentence there too.
