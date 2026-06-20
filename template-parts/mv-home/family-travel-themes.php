<?php
/**
 * Homepage family travel themes — Section 6 (plan-mid.md §4.1).
 *
 * Tiles, same pattern as primary-pathways.php — not a post grid. Each tile
 * links to the calm [travel_finder_focus] view
 * (/nos-idees-de-voyage/?f=slug1,slug2), not the full filter tool, and
 * uses its top-matching post's thumbnail.
 *
 * Backed by mavo-travel-finder's homepage catalog (see
 * includes/homepage-catalog.php in that plugin) — a brainstorm of ~20
 * candidate themes, of which only a curated few are picked below. Swap
 * `$selected_keys` any time; no plugin changes needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVF_Homepage' ) ) {
	return;
}

$selected_keys     = [ 'bebe', 'une_semaine', 'nature_rando', 'plage_cote' ];
$placeholder_image = 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg';
$focus_url          = 'https://www.mamanvoyage.com/nos-idees-de-voyage/';
$lang               = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$items = [];
foreach ( $selected_keys as $key ) {
	$meta = TVF_Homepage::get_card_meta( $key );
	if ( ! $meta ) {
		continue;
	}

	$posts = TVF_Homepage::get_card_posts( $key, $lang, 3 );
	if ( empty( $posts ) ) {
		continue; // no matching posts yet — skip rather than show an empty promise.
	}

	$image = get_the_post_thumbnail_url( $posts[0], 'medium_large' ) ?: $placeholder_image;

	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => add_query_arg( 'f', implode( ',', $meta['slugs'] ), $focus_url ),
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
?>
<section class="mv-section mv-family-travel-themes">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Voyager selon votre famille', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
