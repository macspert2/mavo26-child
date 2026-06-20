# Homepage prototype — integration notes

Built against plan-mid.md Phase 0–2. Live (hidden) at
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
3. The mavo-travel-finder plugin needs two new `require_once` lines in
   `mavo-travel-finder.php` to load `includes/homepage-catalog.php` and
   `includes/class-tvf-homepage.php` (added in that plugin's repo, not
   wired into its bootstrap yet — pending).

## What's real vs. stubbed

- **Real content:** hero, trust-bar, primary-pathways, recent-posts,
  featured-destinations, seasonal-guides, family-travel-themes
  (plan-mid.md §4.1 sections 1, 2, 3, 4, 5, 6, 7).
- **Still stubbed:** about-mini, start-here-cta.
- The last three sections are tiles (same pattern as primary-pathways),
  not post grids: each picks a small curated subset of keys from
  mavo-travel-finder's `includes/homepage-catalog.php` (a brainstorm of
  ~30 candidate cards across all three sections), uses the top-matching
  post's thumbnail, links to a filtered view of the travel-finder page
  (`/ou-partir-trouvez-votre-prochain-voyage/?f=slug1,slug2`), and skips
  itself entirely if zero posts match — no plugin changes needed to swap
  which keys are featured, only the `$selected_keys` array in each
  template part.

## Known placeholders to revisit

- All primary-pathways cards share one placeholder photo
  (`/wp-content/uploads/2024/09/IMG_7174.jpeg`) — swap per-card once real
  destination images are picked. Same placeholder used as the fallback
  image for theme/season/destination tiles when a post has no thumbnail.
- All primary-pathways card URLs are `#` except "France en famille"
  (`/france/`, already live). Update each once its hub/page exists.
- Hero CTAs: "Trouver une idée de voyage" now points at the real
  travel-finder page. "Explorer les destinations" and "Commencer ici"
  still link to `#` — no destinations-overview page or `/commencez-ici/`
  exists yet.
- Trust-bar numbers are the plan's draft figures — confirm real ones.
- `recent-posts.php` passes `lang` to `WP_Query` directly when Polylang is
  active — a minimal, local check, not the shared `mv_current_language()`
  wrapper from plan Phase 3. Promote to a shared helper once more sections
  need language logic.
- No "view all" link on recent-posts — there's no `/blog/` (or equivalent)
  latest-posts page yet (plan Phase 9).
