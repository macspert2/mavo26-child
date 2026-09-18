# mavo audit — what's left

Picked up from a plugin-by-plugin audit of every `mavo-*` component, Sept 2026.
Paused 2026-09-18. Companion files: `audit-notes.md` (child-theme findings),
`sharing-between-plugins.md` (how components should share code).

Nothing here is urgent and nothing here is a security issue.

---

## Commit first — one of these is load-bearing

Uncommitted working-tree changes, all from the audit.

| Repo | File | What |
| --- | --- | --- |
| `mavo-custom-shortcodes` | `tests/harness.php` | **Real fix.** Adds the `plugin_dir_path()` stub. Without it, requiring the plugin is a fatal error and every test in the suite dies on load. Until this is committed the suite is broken in the repo and green only on the machine it was written on. |
| `mavo-for-you` | `includes/class-mavo-for-you-render.php` | Adds `cache_enabler_bypass_cache` beside `DONOTCACHEPAGE` |
| `mavo-for-you` | `tests/run.sh` (untracked) | The suite had 14 files and no runner |
| `mavo-hub-manager` | `includes/class-mavo-hub-manager-audit.php` | Comment only |
| `mavo-dashboard` | `mavo-dashboard.php` | Comment only |
| `mavo-travel-finder` | `class-tvf-store.php`, `class-tvf-popular-snapshots.php` | Comment only |
| `mavo-menu-hc` | `includes/menu-render.php` | Comment only |

The comment-only diffs record that `MAVO_META_VIEWS` is a rolling ~90-day total
written by recent-post-popularity, not a lifetime count — easy to misread as
all-time traffic.

Use `git add -A`: some changes are deletions or new files.

Also uncommitted but **not deployed**, so cosmetic: `mavo-img2picture`,
`mavo-menu`, `mavo-media-clean`, `mavo-cookie-consent`, `autoUpdatingInfo`.

---

## 1. Delete the orphan image files — the big one

~91,000 resized JPEGs at widths nothing uses, ~182,000 counting their `.webp`
sidecars. Seventeen years of intermediates from themes and settings long gone:
`630w` alone is 27,000 files.

**The only irreversible item in the whole audit.** Do not skip the prerequisite.

Old post content references sized URLs directly, including widths no current
theme registers — `630w` was found live in rendered pages. Deleting by width
would break images in old posts silently.

Prerequisite: a scan over `post_content` building the set of filenames actually
referenced, then delete only what is unreferenced **and** outside the ladder
(960/768/640/480/300/225/150). `wp mavo-webp content-widths` already reports
which widths content points at — 17,366 of 17,651 references are to the full
size, only 285 are sized — so the referenced set is small and the scan is cheap.

Suggested shape: `wp mavo-webp orphans [--dry-run] [--delete]`, sharing the
directory-scanning code already in `Mavo_Webp_Files::orphan_sizes()`.

## 2. `mavo-img2picture` — delete it

Confirmed retired. It registers the same three filters at the same priority 9 as
`mavo-img-srcset`; harmless deactivated, genuinely bad if ever activated.

## 3. Child theme — `audit-notes.md` items 1–3

1. **~90 hardcoded `mamanvoyage.com` URLs** across ~20 visitor-facing templates.
   `mv_site_url()` exists for this; `no-results.php` and `inc/mv-landing-footer.php`
   are converted as worked examples. Left undone because it touches ~20 templates
   at once and deserves its own change with its own testing.
2. **Privacy link doesn't ask WordPress.** `inc/mv-landing-footer.php` hardcodes a
   path; `get_privacy_policy_url()` knows the configured page. Check the
   Settings → Privacy value first — it may not be the page in that path.
3. **Can `mv_site_url()` collapse into `home_url()`?** One live check settles it:
   does `home_url( '/a-propos/' )` return the French path from an English page?
   If yes, `mv_site_url()` can go. If it prefixes `/en/`, it must stay — contact,
   legal and privacy are French-only by design.

Items 4 and 5 in that file are deliberately parked; item 5 (`['fr','en','de']`
hardcoded) was reconsidered on 2026-09-18 and left alone — see the mu-plugin
section of `sharing-between-plugins.md`.

## 4. `mavo-img-srcset` — the editorial pass

`process_img()` still carries guards inherited from the responsive layer it no
longer has: JPEG-only, no query string, no Photon URL. They are arbitrary for
what the method now does (alt, alignment classes, centred-`<p>` unwrapping,
`<em>` → `<figcaption>`). Relaxing them widens what gets touched, so it needs its
own before/after check — `tools/snapshot-images.py <url> --check` is the tool.

## 5. Small, when convenient

- `mavo-for-you`: `class-mavo-for-you-render.php` and `class-mavo-for-you-page.php`
  each have their own `current_lang()`. Deliberate (post language, not UI
  language) but worth a comment saying so.
- An alt-coverage count: how many attachments have `_wp_attachment_image_alt`
  stored. Decides whether the media-library alt fallback is doing real work or
  whether the library needs filling in. Deferred 2026-09-18.

---

## Declined — recorded so they don't come back

- **`mavo-core` mu-plugin** for shared helpers. Reasoning in
  `sharing-between-plugins.md`; revisit only if something must run before plugins
  load, or a fourth language is added.
- **`edit_posts` vs `edit_post`** in mavo-travel-finder. Two admins have backend
  access, so admin-only capability gaps are not worth the churn.
- **Cookie banner implied consent.** Deliberate: banners are poor UX and everyone
  accepts them reflexively. Functional cookies are allowlisted before consent.
- **mavo-picture-tag's shared builder** — documented in place, not extracted.
- **mavo-stats item 3** — assessed as very low risk, left.
- Skipped as not deployed: `mavo-ptppg`, `mavo-cookie-consent` (the deployed one
  is `mavo-cookie-banner`), `mavo-menu` (deployed one is `mavo-menu-hc`),
  `mavo-media-clean`.
