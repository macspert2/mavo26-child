# mavo audit — what was done

A plugin-by-plugin audit of every `mavo-*` component, Sept 2026, paused
2026-09-18. What is still outstanding is in `audit-todo.md`; the conventions it
produced are in `sharing-between-plugins.md`; child-theme specifics are in
`audit-notes.md`.

## Scope

Twenty deployed components, one at a time. Five dropped as not deployed:
`mavo-ptppg`, `mavo-cookie-consent` (the live one is `mavo-cookie-banner`),
`mavo-menu` (the live one is `mavo-menu-hc`), `mavo-media-clean`, and
`mavo-img2picture` (retired — still on disk, see the to-do list).

## The recurring theme

Very little was broken logic. Most of it was **code that had quietly stopped
matching reality**:

- a cache fingerprint that could not see the thing that changed
- test harnesses drifted from production, so suites passed over dead code
- dead code that read like live wiring
- logic duplicated across repositories that had to agree character-for-character,
  and did not
- comments naming Swift Performance, a cache the site had already left

---

## The image work

Started as "the hero slider truncates where WordPress rounds" and ended up
rebuilding the responsive image layer across two plugins and the server config.

### What was wrong

A sweep of 42 live posts found **18 image URLs returning 404** — broken images
with no PHP error and nothing in any log. Two causes:

- **`-rotated`**: an EXIF-rotated upload is stored as `IMG_6585-rotated.jpeg`,
  but WordPress names its intermediates after the *un-rotated* base. Every
  portrait phone photo was affected.
- **Rounding**: heights derived from the editor's already-rounded width/height
  attributes landed a pixel away from the filenames on disk
  (`-640x452` derived, `-640x453` real).

### Why it was wrong

**93% of attachments had no `sizes` recorded in their metadata at all.**
`wp_calculate_image_srcset()` therefore had nothing to work from, which is
exactly why the plugin derived filenames by arithmetic. It was not
reimplementing core badly; it was coping with a library WordPress knew nothing
about. The resized files existed on disk — produced out of band, never written
back to the database.

### What was done

- `wp mavo-webp` commands: `status`, `sizes`, `repair-sizes`, `backfill`,
  `content-widths`
- **44,348 sizes recorded** that existed on disk but not in metadata
- **1,225 sidecars backfilled**, 47.8% smaller than their JPEGs
- Responsive layer rewritten to delegate to core, with WebP applied through URL
  filters. **No code constructs an image filename any more**, so neither bug
  class is expressible.
- Hero slider rewritten the same way, deleting the lookup duplicated between the
  two plugins
- nginx `Accept` negotiation removed: Cloudflare's free tier ignores
  `Vary: Accept`, so each URL was pinned to whichever format was fetched first —
  some JPEGs never delivered WebP despite having sidecars, others served WebP to
  browsers that had not asked

### Result

Every URL on the checked pages returns 200. Images the plugin previously skipped
— roughly 19% of content images, those without a `width` attribute — now get
responsive candidates. A `768w` rung appeared that the old hardcoded
960/640/480 ladder never offered.

---

## Everything else

**Correctness fixes** in travel-finder, geotag-plus, hub-manager, dashboard, toc,
contact, indexnow, highlight-comments, cookie-banner, geo-explorer,
google-preferred-sources, custom-shortcodes and auto-feature — including schema
upgrade paths for two plugins whose tables had changed shape since installation.

**Accessibility.** 71% of content images were taking their `alt` from the post's
Yoast focus keyword, so a dozen images in an article all announced the same
phrase — useless to a screen reader and keyword-stuffed to a search engine. Now
sourced from the media library, and left empty when there is none, which is the
correct markup for an image with nothing useful to say about it.

**Security.** SVG uploads removed from the child theme (WordPress does not
sanitise SVG). `.md` files are no longer publicly served — `audit-notes.md` had
been handing out 6 KB of "known issues, not yet fixed" to anyone who asked.

**Tests.** From scattered to **904 assertions across six plugins**, each runnable
with a single command.

**Two features.** Geo breadcrumbs now link to a location's dedicated landing page
rather than its tag archive, in the visible crumb and the JSON-LD alike. And the
components learned to share through filters rather than direct calls — three
worked examples, written up in `sharing-between-plugins.md`.

---

## What the audit got wrong

Recorded because it shaped how the later work was done, and because the same
traps are still there.

- Asserted `mavo-sliders` was not a git repository — three times, from a
  hardcoded label in an `echo` that was never checked.
- Claimed WordPress truncates image dimensions. It rounds. The live files proved
  it, after a comment asserting the opposite had been committed.
- In the final cross-plugin sweep, **three findings did not survive scrutiny**: a
  dead-code check that produced pure noise, a "test suite that cannot fail" that
  turned out to have 355 passing assertions, and an asset-versioning problem that
  did not exist.

The pattern is clear enough to act on: the plugin-by-plugin passes held up
because each finding was read in context. The broad sweep traded that for breadth
and accuracy suffered. Anything in `audit-todo.md` that came from the sweep is
marked as a lead, not a finding, and should be re-read in context before being
acted on.

---

## State at the pause

Everything deployed and verified, except seven uncommitted files listed at the
top of `audit-todo.md` — one of which is load-bearing.

The largest remaining job is deleting roughly 182,000 orphaned image files, which
needs a `post_content` scan first. It is the only irreversible item on any list.
