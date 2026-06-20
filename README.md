# Homepage prototype — integration notes

Built against plan-mid.md Phase 0–1. Copy this folder's contents into the
real GeneratePress child theme, preserving the relative paths:

```
page-accueil-prototype.php          -> theme root
template-parts/mv-home/*.php        -> theme root /template-parts/mv-home/
template-parts/mv-shared/*.php      -> theme root /template-parts/mv-shared/
assets/css/mv-home.css              -> theme root /assets/css/mv-home.css
functions-snippet.php               -> paste contents into functions.php (not a standalone file)
```

## Manual steps (need wp-admin / DB access)

1. Create a WordPress page: title "Accueil prototype", slug `accueil-prototype`.
   WordPress will auto-select `page-accueil-prototype.php` via the template
   hierarchy — no "Page Attributes > Template" assignment needed.
2. Don't add the page to any menu.
3. Paste `functions-snippet.php`'s contents into the theme's functions.php
   (adds noindex + the page-scoped CSS enqueue).
4. Load the page and confirm: no PHP notices/fatals, hero/trust-bar/pathway
   cards render, mobile layout doesn't break.

## What's real vs. stubbed in this round

- **Real content:** hero, trust-bar, primary-pathways (plan-mid.md §4.1
  sections 1–3), using the new `mv-shared/section-header`, `card-link`,
  `grid-wrapper` partials.
- **Stubs (safe no-ops, ready for the next phase):** featured-destinations,
  seasonal-guides, family-travel-themes, recent-posts, about-mini,
  start-here-cta. Each stub file has a comment pointing at the plan section
  and, where relevant, the mavo-travel-finder plugin API it should call
  (`TVF_Store::query_results()`).

## Known placeholders to revisit

- All primary-pathways card URLs are `#` except "France en famille"
  (`/france/`, already live). Update each once its hub/page exists.
- Hero CTAs ("Trouver une idée de voyage", "Explorer les destinations",
  "Commencer ici") link to `#` — point them at the travel finder page,
  a destinations hub, and `/commencez-ici/` respectively once those exist.
- Trust-bar numbers are the plan's draft figures — confirm real ones.
- `mv_current_language()` / Polylang wiring (plan Phase 3) intentionally
  not added yet — nothing in this round needs it, since all content here
  is static French copy.
