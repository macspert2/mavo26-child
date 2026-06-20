<?php
/**
 * Homepage featured destinations — Section 4 (plan-mid.md §4.1).
 *
 * Same tile pattern as primary-pathways.php, linking to the calm
 * [travel_finder_focus] view, not the full filter tool. Limited to the
 * real geographie filter_slugs (france, angleterre, mediterranee, europe,
 * sans_decalage, plus_loin) — the plan's example list (Italie, Espagne,
 * Grèce, Portugal, Écosse, Croatie) doesn't match the real registry;
 * those would need new filter_slugs and data entry before they could
 * appear here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVF_Homepage' ) ) {
	return;
}

$selected_keys     = [ 'france', 'angleterre', 'mediterranee', 'europe' ];
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
<section class="mv-section mv-featured-destinations">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Destinations à la une', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
