<?php
/**
 * French homepage trip-type tiles — hardcoded links to real WordPress
 * categories, same mechanism as mv-home-en/mv-home-de's trip-type.php
 * (not catalog/wp_tvf_post_filter-backed) — added on request to mirror
 * those sections rather than the catalog-tile-grid.php pattern used
 * elsewhere on this homepage, even though FR also has real filter data
 * for this same concept (Start Here's "Quel type de séjour ?" group).
 * Categories/images verified live; each tile's image is that
 * category's own most recent post's featured image.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$trip_types = [
	[
		'title'       => __( 'Randonnée', 'mavo' ),
		'description' => __( 'Randonnées en famille, adaptées aux enfants.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/category/voyages-avec-enfants/rando-voyages-avec-enfants/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2026/04/IMG_1669.jpeg',
	],
	[
		'title'       => __( 'City-trip', 'mavo' ),
		'description' => __( 'Visites culturelles en famille.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/category/voyages-avec-enfants/citytrip/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2025/05/IMG_7333.jpeg.webp',
	],
	[
		'title'       => __( 'Plage', 'mavo' ),
		'description' => __( 'Vacances à la mer avec les enfants.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/category/voyages-avec-enfants/plages/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2026/02/IMG_2571.jpeg',
	],
	[
		'title'       => __( 'Road trip', 'mavo' ),
		'description' => __( 'Nos plus beaux itinéraires en famille.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/category/voyages-avec-enfants/roadtrip/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2026/06/IMG_5870.jpeg',
	],
];

$items = [];
foreach ( $trip_types as $trip_type ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $trip_type['url'],
		'title'       => $trip_type['title'],
		'description' => $trip_type['description'],
		'image'       => $trip_type['image'],
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
}
?>
<section class="mv-section mv-trip-type">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Envie de quel type de voyage ?', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
