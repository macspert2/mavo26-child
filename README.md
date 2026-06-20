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
3. `mavo-travel-finder.php` has the two `require_once` lines for
   `includes/homepage-catalog.php` and `includes/class-tvf-homepage.php`,
   plus a version bump (1.5.2 → 1.5.3) — done directly in that plugin's
   repo, ready to commit/push/pull onto live.
4. Done: page "Nos idées de voyage" (`/nos-idees-de-voyage/`) created with
   `[travel_finder_focus]`, noindexed via Yoast, not in any menu.
   `TVF_Focus` enqueues this theme's `assets/css/mv-home.css` itself, so
   no theme-side wiring was needed beyond that CSS file existing.

## What's real vs. stubbed

All 9 sections from plan-mid.md §4.1 / Phase 16's MVP list are now built:
hero, trust-bar, primary-pathways, featured-destinations, seasonal-guides,
family-travel-themes, recent-posts, about-mini, start-here-cta.

- **about-mini** and **start-here-cta** are the newest — about-mini has
  real (draft) editorial copy; start-here-cta is a structural scaffold
  only (real copy from plan §4.3, but its CTA link is `#` since
  `/commencez-ici/` doesn't exist yet).
- featured-destinations / seasonal-guides / family-travel-themes are tiles
  (same pattern as primary-pathways), not post grids: each picks a small
  curated subset of keys from mavo-travel-finder's
  `includes/homepage-catalog.php` (a brainstorm of ~30 candidate cards
  across all three sections), uses the top-matching post's thumbnail,
  links to the calm `[travel_finder_focus]` view
  (`/nos-idees-de-voyage/?f=slug1,slug2` — not the full filter tool), and
  skips itself entirely if zero posts match — no plugin changes needed to
  swap which keys are featured, only the `$selected_keys` array in each
  template part.
- Hero's "Trouver une idée de voyage" CTA deliberately still points at
  the full `/ou-partir-trouvez-votre-prochain-voyage/` tool, not the focus
  page — it has no pre-selected filter, and the focus page redirects to
  the homepage when `f` is empty.

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
