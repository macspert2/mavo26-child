<?php
/**
 * A full section of catalog-backed tiles (TVF_Homepage) — section-header
 * + grid of card-link tiles, one per catalog key. Owns its own
 * <section>/<container> wrapper so it can skip itself entirely (no empty
 * shell) when the plugin is inactive or none of the keys have matches.
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
 *       'section_class'  => 'mv-family-travel-themes',
 *       'title'          => 'Voyager selon votre famille',
 *       'keys'           => [ 'bebe', 'une_semaine', 'nature_rando' ],
 *       'columns'        => 4,
 *       'posts_per_key'  => 6,      // optional, how many posts to check per key
 *       'link_to'        => 'focus', // optional: 'focus' (default) or 'full'
 *       'url_overrides'  => [ 'france' => 'https://www.mamanvoyage.com/france/' ], // optional, per-key URL override
 *       'background'     => 'cream', // optional, plan2.md §10.3 — unset/'' = unchanged (white)
 *   ] );
 *
 * `posts_per_key` is also the candidate pool for image de-duplication
 * (see below) — too small and overlapping keys (e.g. France/Europe/no
 * jet-lag are all true of the same posts) can exhaust every candidate's
 * image and fall back to a repeat.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVF_Homepage' ) ) {
	return;
}

$section_class = $args['section_class'] ?? '';
$title         = $args['title'] ?? '';
$keys          = $args['keys'] ?? [];
$columns       = (int) ( $args['columns'] ?? 4 );
$posts_per_key = (int) ( $args['posts_per_key'] ?? 6 );
$link_to       = $args['link_to'] ?? 'focus';
$url_overrides = $args['url_overrides'] ?? [];
$background    = $args['background'] ?? '';

if ( empty( $keys ) ) {
	return;
}

$placeholder_image = mv_get_placeholder_image();
$lang               = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

// Per-language link targets — only French has a full-finder page today;
// other languages fall back to French rather than 404. Add an entry here
// once an EN/DE full-finder page exists.
$focus_urls = [
	'fr' => 'https://www.mamanvoyage.com/nos-idees-de-voyage/',
	'en' => 'https://www.mamanvoyage.com/en/our-travel-ideas/',
	'de' => 'https://www.mamanvoyage.com/de/unsere-reiseideen/',
];
$full_urls  = [
	'fr' => 'https://www.mamanvoyage.com/ou-partir-trouvez-votre-prochain-voyage/',
];
$link_base  = 'full' === $link_to
	? ( $full_urls[ $lang ] ?? $full_urls['fr'] )
	: ( $focus_urls[ $lang ] ?? $focus_urls['fr'] );

$items       = [];
$used_images = []; // avoid repeating the same photo twice within this section.

foreach ( $keys as $key ) {
	$meta = TVF_Homepage::get_card_meta( $key, $lang );
	if ( ! $meta ) {
		continue;
	}

	$posts = TVF_Homepage::get_card_posts( $key, $lang, $posts_per_key );
	if ( empty( $posts ) ) {
		continue; // no matching posts yet — skip rather than show an empty promise.
	}

	// Prefer the top post's image, but if it was already used earlier in
	// this section (the same post ranks #1 for more than one tile), fall
	// through to the next-ranked post that has an unused image instead.
	$image          = null;
	$is_placeholder = false;
	foreach ( $posts as $candidate ) {
		$candidate_image = get_the_post_thumbnail_url( $candidate, 'medium_large' );
		if ( $candidate_image && ! in_array( $candidate_image, $used_images, true ) ) {
			$image = $candidate_image;
			break;
		}
	}
	if ( ! $image ) {
		$image = get_the_post_thumbnail_url( $posts[0], 'medium_large' );
		if ( ! $image ) {
			$image          = $placeholder_image;
			$is_placeholder = true;
		}
	}

	// Track every real photo, even a forced repeat or one that happens to
	// be the same file as $placeholder_image — only the synthetic "no
	// photo exists at all" fallback should be exempt, so unrelated tiles
	// can all safely share it without seeming to "block" each other.
	if ( ! $is_placeholder ) {
		$used_images[] = $image;
	}

	$url = $url_overrides[ $key ] ?? add_query_arg( 'f', implode( ',', $meta['slugs'] ), $link_base );

	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $url,
		'title'       => $meta['label'],
		'description' => $meta['description'],
		'image'       => $image,
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
}

if ( empty( $items ) ) {
	return;
}

$section_classes = 'mv-section ' . $section_class;
if ( $background ) {
	$section_classes .= ' mv-section--bg-' . sanitize_html_class( $background );
}
?>
<section id="<?php echo esc_attr( $section_class ); ?>" class="<?php echo esc_attr( $section_classes ); ?>">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => $title,
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => $columns,
			'items'   => $items,
		] );
		?>
	</div>
</section>
