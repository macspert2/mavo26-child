<?php
/**
 * Homepage featured destinations — Section 4 (plan-mid.md §4.1).
 *
 * Hardcoded landing-page links, not catalog/wp_tvf_post_filter-backed —
 * each of these 4 destinations has its own dedicated page, so this
 * section just links straight there (same pattern as primary-pathways.php),
 * instead of going through TVF_Homepage/catalog-tile-grid.php. Each has
 * its own real photo now too.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$destinations = [
	[
		'title'       => __( 'France', 'mavo' ),
		'description' => __( 'Week-ends, villages, nature, vélo et vacances sans partir trop loin.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/france/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2018/01/IMG_0745.jpg',
	],
	[
		'title'       => __( 'Angleterre', 'mavo' ),
		'description' => __( 'Nos idées depuis notre vie d’expatriés : Londres, sud de l’Angleterre, nature et escapades en train.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/angleterre/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2026/05/IMG_6132.jpeg.webp',
	],
	[
		'title'       => __( 'Italie', 'mavo' ),
		'description' => __( 'City-trips, îles, randonnées et road trips gourmands avec enfants.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/italie/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2010/03/26023_408646857888_508957888_4904843_5309085_n.jpg',
	],
	[
		'title'       => __( 'Espagne', 'mavo' ),
		'description' => __( 'Baléares, Catalogne, villes et soleil — testés en famille.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/espagne/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2019/10/P9031799.jpeg',
	],
];

$items = [];
foreach ( $destinations as $destination ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $destination['url'],
		'title'       => $destination['title'],
		'description' => $destination['description'],
		'image'       => $destination['image'],
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
