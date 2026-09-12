# Implementation brief: automatic post hub navigation

## Objective and scope

Implement a compact, structural hub navigation strip on single blog posts, after the complete comments area and before the content-area end/footer. Use the existing child theme; GeneratePress Lite remains unchanged. This is deterministic navigation based on post metadata, not personalized recommendations.

Inspect the child theme's current `single.php`, `functions.php`, stylesheet loading, and existing hub helpers before editing. Reuse compatible existing helpers and avoid name collisions. Do not replace the current template with a copy of the parent template.

## Data and behavior

Read the current queried post's `_mavo_primary_geo_hub` and `_mavo_primary_theme_hub` relationships. The suggested code assumes each contains one positive post ID (integer or digit string); verify the existing storage format. If different, adapt a small resolver to that documented format rather than guessing or accepting arbitrary URLs.

A valid target must exist, have status `publish`, not be password protected, and have `_mavo_hub_type` exactly equal to its expected type (`geo` or `theme`). Use its title as clear link text and its permalink as the destination. Do not hardcode a hub post type unless the existing implementation requires one. Omit invalid, untitled, or URL-less targets individually. Render no wrapper when no valid links remain. Order geo before theme and deduplicate target IDs defensively. A single target with one type cannot normally validate for both slots, but retain the guard.

Use the relationships on the current post, including translated posts. Do not silently substitute another language's hub or introduce automatic translation lookup. Label the strip according to Polylang's current language, with locale fallback when Polylang is unavailable: FR `Continuer à explorer`, EN `Continue exploring`, DE `Weiter entdecken`; default to French for unsupported languages.

## Suggested child-theme PHP

Add these functions to the existing `functions.php`, within its PHP context (do not add a second opening tag). Adapt names if already present.

```php
function mavo_hub_nav_label() {
    $language = function_exists( 'pll_current_language' )
        ? pll_current_language( 'slug' )
        : '';
    if ( ! is_string( $language ) || '' === $language ) {
        $language = get_locale();
    }
    $parts = preg_split( '/[-_]/', strtolower( $language ) );
    $labels = array(
        'fr' => 'Continuer à explorer',
        'en' => 'Continue exploring',
        'de' => 'Weiter entdecken',
    );
    return isset( $labels[ $parts[0] ] ) ? $labels[ $parts[0] ] : $labels['fr'];
}

function mavo_hub_nav_target( $post_id, $type ) {
    if ( ! in_array( $type, array( 'geo', 'theme' ), true ) ) {
        return null;
    }
    $raw = get_post_meta( $post_id, '_mavo_primary_' . $type . '_hub', true );
    // Reject malformed IDs, arrays, negative values, decimals, and URLs.
    if ( ! is_scalar( $raw ) || ! preg_match( '/^[1-9][0-9]*$/D', (string) $raw ) ) {
        return null;
    }
    $id = filter_var( $raw, FILTER_VALIDATE_INT, array(
        'options' => array( 'min_range' => 1 ),
    ) );
    if ( false === $id ) {
        return null;
    }
    $hub = get_post( $id );
    if ( ! $hub || 'publish' !== $hub->post_status || '' !== $hub->post_password
        || $type !== get_post_meta( $id, '_mavo_hub_type', true ) ) {
        return null;
    }
    $title = trim( wp_strip_all_tags( get_the_title( $id ) ) );
    $url = get_permalink( $id );
    if ( '' === $title || ! $url || '' === esc_url( $url ) ) {
        return null;
    }
    return array( 'id' => $id, 'type' => $type, 'title' => $title, 'url' => $url );
}

function mavo_render_hub_nav() {
    if ( ! is_singular( 'post' ) || post_password_required( get_queried_object_id() ) ) {
        return;
    }
    $post_id = get_queried_object_id();
    $links = array();
    $seen = array();
    foreach ( array( 'geo', 'theme' ) as $type ) {
        $hub = mavo_hub_nav_target( $post_id, $type );
        if ( $hub && ! isset( $seen[ $hub['id'] ] ) ) {
            $links[] = $hub;
            $seen[ $hub['id'] ] = true;
        }
    }
    if ( ! $links ) {
        return;
    }
    $label = mavo_hub_nav_label();
    ?>
    <nav class="mavo-hub-nav" aria-label="<?php echo esc_attr( $label ); ?>">
        <span class="mavo-hub-nav__label"><?php echo esc_html( $label ); ?></span>
        <ul class="mavo-hub-nav__links">
            <?php foreach ( $links as $hub ) : ?>
                <li class="mavo-hub-nav__item">
                    <a class="mavo-hub-nav__hub mavo-hub-nav__hub--<?php echo esc_attr( $hub['type'] ); ?>"
                       href="<?php echo esc_url( $hub['url'] ); ?>">
                        <span class="mavo-hub-nav__icon" aria-hidden="true">
                            <?php if ( 'geo' === $hub['type'] ) : ?>
                                <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M12 21s7-7 7-12a7 7 0 1 0-14 0c0 5 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                            <?php else : ?>
                                <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m16 8-2.5 5.5L8 16l2.5-5.5Z"/></svg>
                            <?php endif; ?>
                        </span>
                        <span class="mavo-hub-nav__text"><?php echo esc_html( $hub['title'] ); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php
}
```

The resulting HTML is one named navigation landmark containing the visible label and a list of one or two ordinary anchors. The complete hub label and icon form one clickable area. Inline decorative SVGs need no library or external asset. Do not add `target`, `nofollow`, click handlers, descriptions, or UI labels such as “Géographique” and “Thématique”.

## Placement: choose exactly one route

Trace where `comments_template()` runs and where `.site-main` closes in the actual child template, including called template parts. Insert the strip after comments and their form/pagination, outside any condition requiring comments to exist or be open. Posts with closed comments or no comments must still show their valid hub links.

If the current `single.php` provides a straightforward location inside the main content area immediately after comment output, add only:

```php
<?php mavo_render_hub_nav(); ?>
```

Keep existing loop, navigation, sidebar, wrapper, and footer logic intact. Do not place it after `get_footer()` or inside the comment list/form.

If the structure makes a hook safer, prefer a child-theme callback on an existing GeneratePress-compatible hook. `generate_after_main_content` is documented as occurring before the closing `.site-main` element. Verify that the installed child template invokes it once, after comments, and inside the intended content area before using:

```php
add_action( 'generate_after_main_content', 'mavo_render_hub_nav', 20 );
```

This uses WordPress hooks and does not require GeneratePress Premium. Do not also insert the direct call. Inspect callback priorities if other content uses this hook. Do not assume `generate_after_entry_content` is after comments; do not use a footer hook that places the strip outside the main content column. If the documented hook is missing or misplaced in the child template, use the minimal direct insertion at the verified location rather than copying parent logic or editing GeneratePress.

## Suggested CSS

Add to the child theme's already enqueued stylesheet. Preserve its existing loading setup. Match the current content gutter with a local adjustment if needed; do not change parent selectors globally.

```css
.mavo-hub-nav {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem 1rem;
    margin-block: 1.5rem;
    padding: 1rem;
    background: #faf7f1;
    border: 1px solid #e5ddd2;
    border-radius: .75rem;
    color: #453f38;
}
.mavo-hub-nav__label {
    font-size: .8125rem;
    font-weight: 600;
    line-height: 1.5;
}
.mavo-hub-nav .mavo-hub-nav__links {
    display: flex;
    flex: 1 1 18rem;
    flex-wrap: wrap;
    gap: .5rem;
    list-style: none;
    margin: 0;
    padding: 0;
    min-width: 0;
}
.mavo-hub-nav__item { margin: 0; min-width: 0; max-width: 100%; }
.mavo-hub-nav .mavo-hub-nav__hub {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    max-width: 100%;
    box-sizing: border-box;
    min-height: 2.75rem;
    padding: .5rem .75rem;
    border: 1px solid #dfd7cd;
    border-inline-start: 3px solid var(--mavo-hub-accent);
    border-radius: .5rem;
    background: #fffdf9;
    color: #453f38;
    line-height: 1.4;
    text-decoration: none;
}
.mavo-hub-nav__hub--geo { --mavo-hub-accent: #4e74a5; }
.mavo-hub-nav__hub--theme { --mavo-hub-accent: #886353; }
.mavo-hub-nav__icon {
    display: inline-flex;
    flex: 0 0 1.125rem;
    color: var(--mavo-hub-accent);
}
.mavo-hub-nav__icon svg {
    width: 1.125rem;
    height: 1.125rem;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.75;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.mavo-hub-nav__text { min-width: 0; overflow-wrap: anywhere; }
.mavo-hub-nav .mavo-hub-nav__hub:hover {
    color: #453f38;
    background: #f4efe6;
    text-decoration: underline;
}
.mavo-hub-nav .mavo-hub-nav__hub:focus-visible {
    outline: 2px solid #453f38;
    outline-offset: 3px;
    text-decoration: underline;
}
@media (max-width: 600px) {
    .mavo-hub-nav { align-items: stretch; flex-direction: column; }
    .mavo-hub-nav .mavo-hub-nav__links {
        flex: 0 1 auto;
        flex-direction: column;
    }
    .mavo-hub-nav .mavo-hub-nav__hub { display: flex; width: 100%; }
}
```

Use the blue and brown as subtle accents; keep text dark for contrast. Do not use `#a92d87` anywhere in this component, including inherited hover treatments. Verify actual theme CSS does not override these scoped styles. Desktop wraps naturally; mobile stacks the label and links. No fixed height, truncation, descriptions, large imagery, or horizontal scrolling.

## Acceptance criteria

- No metadata or two invalid targets: no strip, empty landmark, or leftover spacing.
- Geo only, theme only, and both valid: correct links; geo precedes theme; exactly one strip.
- Missing/deleted, draft, pending, private, trashed, scheduled, password-protected, malformed, and type-mismatched targets are omitted; another valid target still renders.
- A shared ID never produces two anchors. Do not relax type validation to force both slots to display.
- Labels match FR/EN/DE current language. Without Polylang, locale fallback works without a fatal error. Relationship destinations remain those assigned to the current post.
- Source HTML contains escaped, real permalink anchors without JavaScript execution. Links open in the same tab and have meaningful hub titles; no new `nofollow`.
- Special characters in titles and URLs render safely. Icons are decorative and do not add screen-reader noise; keyboard focus is clearly visible.
- Strip follows the complete comment section and remains inside the main content area before the footer on posts with comments, no comments, and closed comments.
- Archives, pages, feeds, and password-locked posts do not acquire this strip.
- At 320px and desktop widths, long FR/EN/DE titles wrap without overflow. Verify keyboard use and 200% zoom against the actual theme.
- Parent GeneratePress files remain untouched; existing post layout, comments, sidebar, footer, and other navigation continue to work.

Run PHP syntax checks on changed PHP files and review representative rendered pages plus their source. Use existing test infrastructure where available; do not create a new testing framework for this component. Report changed child-theme files, chosen placement route, validation performed, and any remaining issue.

## Non-goals

No parent-theme edits, GeneratePress Premium dependency, metadata migration or editor controls, automatic hub assignment, translation remapping, personalized recommendations, related-post queries, descriptions, thumbnails, JavaScript/AJAX, tracking, new-tab links, structured-data additions, or redesign of other hub modules.

## Reference documentation

- [GeneratePress: generate_after_main_content](https://docs.generatepress.com/article/generate_after_main_content/) — documented boundary; verify actual installed template placement.
- [GeneratePress: Hooks overview](https://docs.generatepress.com/article/hooks-overview/) — child-theme callbacks without modifying parent files.
- [Polylang: developer how-to](https://polylang.pro/documentation/support/developers/developpers-how-to/) — current language and locale.

