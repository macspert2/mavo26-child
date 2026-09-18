# Sharing code between mavo components

Written 2026-09-18, after an audit of every `mavo-*` plugin found the same
mistake made several times: the same logic written out twice in two repositories,
with nothing keeping the copies in step.

It lives here, next to `audit-notes.md`, because the child theme is the one
component always present and the only version-controlled place estate-wide notes
already live. It is not about the theme. Move it if a better home appears.

## The constraint that shapes everything

Every plugin is its own git repository, deployed on its own. There is no build
step, no autoloader spanning components, and no guaranteed load order beyond
"mu-plugins, then plugins alphabetically, then the theme".

So `Other_Plugin::helper()` is not a shared function. It is a hard dependency
that white-screens the site the moment that plugin is deactivated, renamed, or
deployed a minute later than its consumer.

Three patterns work. Prefer them in this order.

---

## 1. A WordPress filter — preferred

The consumer computes a sensible answer and passes it through a filter. Whoever
can improve it, does. Neither side knows the other exists.

**Worked example — WebP image URLs.** `mavo-img-srcset` registers:

```php
add_filter( 'wp_calculate_image_srcset',   [ __CLASS__, 'filter_srcset' ] );
add_filter( 'wp_get_attachment_image_src', [ __CLASS__, 'filter_src' ] );
```

`mavo-sliders` calls core's `wp_get_attachment_image()` and receives `.webp`
URLs without a line of code referring to the other plugin. Deactivate
`mavo-img-srcset` and the hero serves JPEGs — worse, not broken.

Note this one uses **core's own filters**. Look there first; a surprising amount
is already filterable, and a core filter costs nothing to declare.

**Worked example — breadcrumb links.** `mavo-geotag-plus` does not know that some
geo tags have a landing page:

```php
$url = apply_filters( 'mavo_geo_term_url', $url, $term_id, $lang );
```

`mavo26-child` answers with the page it stored in term meta. The breadcrumb never
learns the meta key; the theme never learns how breadcrumbs are built.

**Use when:** the consumer can produce a working default on its own.

---

## 2. An action, for the other direction

When the *provider's* data changes and something elsewhere has to react.

**Worked example.** Setting a geo tag's landing page fires:

```php
do_action( 'mavo_geo_term_url_changed', $term_id, $page_id, $old_page_id );
```

`mavo-geotag-plus` listens and drops its cached breadcrumbs.

**This is the half that gets forgotten**, and it is the one that produces silent
bugs. A filter that returns a better answer is worthless if the old answer sits
in a cache whose fingerprint cannot see the change. See the section on caching.

---

## 3. `is_callable()` with a documented fallback

When you need something only the other component can compute and there is no
natural filter.

**Worked example** — `GeoBreadcrumb::world_url()`:

```php
if ( is_callable( [ '\TVF_Focus', 'full_finder_url' ] ) ) {
    $url = (string) \TVF_Focus::full_finder_url( $lang );
    if ( $url !== '' ) {
        return $url;
    }
}

return self::WORLD_URLS[ $lang ] ?? self::WORLD_URLS['fr'];
```

Rules: guard with `is_callable()`, always keep a working fallback, and write down
in a comment *why* the fallback is what it is. A fallback nobody can justify gets
deleted by the next reader.

---

## What not to do: copy the function

Two copies have to agree character-for-character forever, and nothing enforces
it. They always drift, and the drift is silent.

The audit's worst case: deriving resized-image filenames by arithmetic, written
once in `mavo-img-srcset` and again in `mavo-sliders`. One rounded, the other
truncated. A sweep of the live site found **18 image URLs returning 404** across
42 posts — broken images, no PHP error, nothing in any log. The fix was to stop
deriving filenames at all and let WordPress supply them, at which point the
duplication had nothing left to duplicate.

The lesson generalises: when two components need the same derived value, the
question to ask first is not "where do we put the shared function" but **"why are
we deriving this at all, instead of asking whoever already knows?"**

---

## Caching: the trap that comes with sharing

Any shared value that gets cached needs an invalidation path, and the cache key
usually cannot see the thing that changed.

`GeoBreadcrumb` fingerprints cached breadcrumbs on `place_id + lang`,
deliberately blind to names and URLs so a re-tag resolving to the same place
keeps hand-edited links. The cost is that repointing a tag at a landing page
changes nothing the fingerprint can see, and the old URL would survive in every
cached breadcrumb — and in its JSON-LD — indefinitely. Hence the action above.

Before shipping anything that changes a shared value, ask: **who has already
cached the old answer, and what tells them?**

---

## Considered and declined: a shared mu-plugin

September 2026. The idea was a `mavo-core` mu-plugin holding common helpers,
starting with the twelve per-plugin "what language is this page" resolvers.

Declined, because:

- **No observed bug.** Not one problem in the whole audit traced to those
  resolvers.
- **They are not really duplicates.** They differ in their fallback when Polylang
  cannot answer — assume `fr`, ask `get_locale()`, or read `/en/` from the URL —
  and each choice is defensible for its plugin. Consolidating means imposing one.
- **Those paths never run.** Polylang is always present in production and always
  returns fr, en or de.
- **A guarded dependency reinstates the duplication.** Wrap the call in
  `function_exists()` with a local fallback and you have kept all the copies and
  added a layer.
- **Filters already solved it twice**, degrading instead of fataling.

Revisit if either becomes true:

- Something must run **before plugins load** — a constant, a kill switch, an
  early conflict fix. A filter cannot do that; an mu-plugin can.
- **A fourth language is added.** Then the fallbacks, and the `fr`/`en`/`de`
  allowlists scattered across roughly thirty files, become a real problem worth
  one source of truth.
