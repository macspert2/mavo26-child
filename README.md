# Homepage prototype — integration notes

Built against plan-mid.md Phase 0–1. Live (hidden) at
https://www.mamanvoyage.com/accueil-prototype/

## Manual steps already done

1. WordPress page created: title "Accueil prototype", slug `accueil-prototype`,
   not in any menu. `page-accueil-prototype.php` is picked up via the slug
   fallback in the template hierarchy — the page's own template assignment
   stays "Default" (confirmed via `page-template-default` in `body class`).
2. `functions.php` has the noindex + CSS enqueue hooks merged in, both
   gated on `is_page('accueil-prototype')` — **not** `is_page_template()`,
   which doesn't fire for slug-fallback templates that were never
   explicitly assigned in Page Attributes (this caused the CSS to silently
   not load in the first round; now fixed).

## What's real vs. stubbed

- **Real content:** hero, trust-bar, primary-pathways, recent-posts
  (plan-mid.md §4.1 sections 1, 2, 3, 7), using `mv-shared/section-header`,
  `card-link`, `card-post`, `grid-wrapper` partials.
- **Stubs (safe no-ops, ready for the next phase):** featured-destinations,
  seasonal-guides, family-travel-themes, about-mini, start-here-cta. Each
  stub file has a comment pointing at the plan section and, where relevant,
  the mavo-travel-finder plugin API it should call (`TVF_Store::query_results()`).

## Known placeholders to revisit

- All primary-pathways cards share one placeholder photo
  (`/wp-content/uploads/2024/09/IMG_7174.jpeg`) — swap per-card once real
  destination images are picked.
- All primary-pathways card URLs are `#` except "France en famille"
  (`/france/`, already live). Update each once its hub/page exists.
- Hero CTAs ("Trouver une idée de voyage", "Explorer les destinations",
  "Commencer ici") link to `#` — point them at the travel finder page,
  a destinations hub, and `/commencez-ici/` respectively once those exist.
- Trust-bar numbers are the plan's draft figures — confirm real ones.
- `recent-posts.php` passes `lang` to `WP_Query` directly when Polylang is
  active — a minimal, local check, not the shared `mv_current_language()`
  wrapper from plan Phase 3. Promote to a shared helper once more sections
  need language logic.
- No "view all" link on recent-posts — there's no `/blog/` (or equivalent)
  latest-posts page yet (plan Phase 9).
