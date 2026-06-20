<?php
/**
 * Homepage featured destinations — Section 4 (plan-mid.md §4.1).
 *
 * Hardcoded landing-page links, not catalog/wp_tvf_post_filter-backed —
 * each of these 4 destinations has its own dedicated page, so this
 * section just links straight there (same pattern as primary-pathways.php),
 * instead of going through TVF_Homepage/catalog-tile-grid.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$placeholder_image = 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg';

$destinations = [
	[
		'title'       => __( 'France', 'mavo' ),
		'description' => __( 'Nos meilleures idées pour voyager en France en famille.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/france/',
	],
	[
		'title'       => __( 'Angleterre', 'mavo' ),
		'description' => __( 'Vivre et voyager en Angleterre avec des enfants.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/angleterre/',
	],
	[
		'title'       => __( 'Italie', 'mavo' ),
		'description' => __( 'Voyager en Italie en famille.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/italie/',
	],
	[
		'title'       => __( 'Espagne', 'mavo' ),
		'description' => __( 'Voyager en Espagne en famille.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/espagne/',
	],
];

$items = [];
foreach ( $destinations as $destination ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $destination['url'],
		'title'       => $destination['title'],
		'description' => $destination['description'],
		'image'       => $placeholder_image,
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
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
