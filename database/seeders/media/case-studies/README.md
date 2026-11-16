# Case-study screenshots

Optimised WebP screenshots of the live public sites, used by `CaseStudiesSeeder` (copied to
`storage/app/public/projects/<slug>/` on seed). Originals (2x PNG) are not in git.

Captured 2026-11-16 with Playwright, viewport only: desktop 1440×900 and mobile 390×844 at 2x,
then scaled to 1600 px (desktop) and 780 px (mobile) wide, WebP quality 80.

| Folder | Source | Notes |
|---|---|---|
| `serverscope/` | info.serverscope.nl (public site) and serverscope.nl/demo | The demo uses sample data (team "Acme (demo)", example.com hosts); its "demo with sample data" banner is kept in the image. No real servers, IPs or accounts. |
| `barbercuts/` | barbercuts.nl home, /prijzen, /aanmelden (empty form) | The phone mock-up on the home page shows names such as "Sam" and "Daan de Vries": confirm these are illustrative, not real customers (REVIEW.md). The public shop directory is deliberately not captured. |
| `jebbari/` | jebbari.nl/nl home (light and dark via its theme toggle), /nl/diensten, /nl/aanvraag/stap/1 (empty form) | |

Re-capture: see `REVIEW.md` → "Screenshots".
