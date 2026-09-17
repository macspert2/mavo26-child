# Child theme — known issues, not yet fixed

From a read-through of the whole of `mavo26-child` on 2026-09-17, as part of a
pass over every `mavo-*` plugin. Everything listed here was **found and
deliberately left alone**; the items fixed in the same pass are at the bottom so
this file records the whole picture rather than half of it.

Nothing here is a security issue and nothing here is urgent.

---

## 1. ~90 hardcoded `https://www.mamanvoyage.com/…` URLs

The largest outstanding item, and the reason this file exists.

| Kind | Roughly | Where |
| --- | --- | --- |
| Page / category / tag links | 57 | `template-parts/mv-search-sidebar.php` (10), `template-parts/mv-shared/catalog-tile-grid.php` (7), `template-parts/mv-home/featured-destinations.php` (5), the three `trip-type.php` variants (4 each), both `destinations.php` variants (4 each), `inc/mv-search-page.php` (4), `template-parts/mv-home/hero.php` (3), `recent-posts.php` (3), `inc/mv-badges.php` (3), `template-parts/mv-start-here/*` (4), and singles elsewhere |
| Image URLs into `/wp-content/uploads/` | 33 | the same files, plus `inc/mv-settings.php:67` (the default placeholder image) |

Why it matters: a staging copy sends its readers to production, and a domain
change would leave every one of these pointing at the old domain. The page links
are the ones that matter; the upload URLs are lower value and defensible as
absolute.

The fix is mechanical — `mv_site_url()` in `functions.php` already exists for
exactly this, and `no-results.php` and `inc/mv-landing-footer.php` have been
converted as worked examples. It is left undone because it touches ~20
visitor-facing templates at once, which deserves to be its own change with its
own testing rather than a line in a tidy-up.

**Three of those URLs are the travel-finder pages** — `inc/mv-badges.php:1016-1018`,
`mv-search-sidebar.php:73-75`, `catalog-tile-grid.php:54-61`. The same three
pages are also hardcoded in `mavo-travel-finder` (as a fallback) and were in
`mavo-geotag-plus`. Both of those now resolve the page properly, and
`TVF_Focus::full_finder_url( $lang )` is a public static resolver these three
sites could call instead — see how `GeoBreadcrumb::world_url()` does it.

## 2. The privacy link does not ask WordPress

`inc/mv-landing-footer.php` links to a hardcoded privacy-policy path.
WordPress knows the page set in Settings → Privacy via
`get_privacy_policy_url()`, and `MFY_Data::privacy_page_ids()` in mavo-for-you
already relies on that setting — so the two would agree, and the theme would
stop hardcoding a path that an editor can change.

Not done because it could change *which* page the link points at, if the
configured privacy page is not the one in that path. Check the setting first.

## 3. `mv_site_url()` may be able to collapse into `home_url()`

`mv_site_url()` builds from `get_option( 'home' )` rather than `home_url()`,
because Polylang runs in directory mode here and filters `home_url()`. It was
not possible to confirm from the code alone whether it rewrites a call that
already carries a path — and if it does, `home_url( '/a-propos/' )` on an English
page becomes `/en/a-propos/`, which would break exactly the links that are meant
to stay French (contact, legal and privacy are French-only by design).

If `home_url( '/a-propos/' )` is confirmed to return the French path from an
English page on the live site, `mv_site_url()` can be deleted and its call sites
switched to `home_url()`.

## 4. `mv_shortcode_tile()` runs `url_to_postid()` per tile

`functions.php`. One rewrite-rule run plus a query for every tile in a
`[mv-tile-grid]`, purely to decide whether to render badges. Page caching
amortises it, so it only becomes worth addressing if a tile grid ever has to
render uncached.

## 5. `[ 'fr', 'en', 'de' ]` hardcoded

`page-blog.php:161`, `inc/mv-settings.php:439`, `inc/mv-badges.php:366` and
`:1020`. Part of a pattern across the whole project — the same triple is written
out in mavo-travel-finder, mavo-geotag-plus, mavo-hub-manager and mavo-for-you
too. `MFY_Config::site_langs()` (asks Polylang, falls back to the triple) is the
best version of it anywhere in the project. Not worth fixing in one codebase
alone.

---

## Deliberate — do not "fix"

- `wp_polyfill` dequeued, and `wp_enqueue_global_styles` removed.
- `mv-custom.css` enqueued at priority 999. The comment above it explains why it
  has to print last; keep the comment if the priority ever changes.
- Feeds bypass the page cache (`cache_enabler_bypass_cache`).
- EN and DE contact/legal/privacy links point at the French pages — those pages
  exist only in French.
- `no-results.php` matches GeneratePress byte-for-byte outside the `is_search()`
  branch, on purpose, so it can be diffed against the parent theme on upgrade.

## Fixed in the same pass, for the record

- **SVG uploads removed.** `upload_mimes` was adding `image/svg+xml` with no
  sanitising; WordPress does not sanitise SVG, and nothing in the theme
  referenced an uploaded `.svg`.
- **`_mv_search_geo_type_map()`** now guards on `\GeoTagger\PlaceRepository` and
  reads through it, instead of querying `{$wpdb->prefix}geo_tagger_places`
  directly with no guard at all.
- **`publish_later_on_feed()`** now takes `$query`, checks `is_main_query()`,
  uses `$wpdb->prepare()`, and compares `post_date_gmt` against a cutoff computed
  in PHP so the index can be used. Hold time filterable via
  `mavo_feed_hold_minutes`.
- **`mv_asset_version()`** replaces three bare `filemtime()` calls that would
  warn and return `false` on a missing file.
- **`wp_date( 'Y' )`** in the copyright line, which used the server timezone.
- **Hardcoded URLs** converted in `no-results.php` and `inc/mv-landing-footer.php`.
- **The landing-page slug list** is now only in `mv_is_landing_page()`; it had a
  second copy in `functions.php`, in a different order.
- **`MAVO_FEED_EXCLUDED_CATEGORY`** replaces a bare `-8467` with a named,
  filterable constant and a note on how to verify which category it is.
