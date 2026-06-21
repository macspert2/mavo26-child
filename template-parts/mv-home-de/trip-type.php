<?php
/**
 * German homepage trip-type tiles — same rationale as
 * template-parts/mv-home-en/trip-type.php. Each now has its own real
 * photo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$trip_types = [
	[
		'title'       => __( 'Wandern', 'mavo' ),
		'description' => __( 'Wanderungen und Naturerlebnisse mit der Familie.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/wandern/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2023/04/IMG_1924.jpeg.webp',
	],
	[
		'title'       => __( 'Städtereisen', 'mavo' ),
		'description' => __( 'Städtetrips mit Kindern.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/staedtereise/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2024/10/IMG_8258.jpeg.webp',
	],
	[
		'title'       => __( 'Natur', 'mavo' ),
		'description' => __( 'Natur erleben mit der Familie.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/natur/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2022/03/Plitvice.jpeg.webp',
	],
	[
		'title'       => __( 'Roadtrips', 'mavo' ),
		'description' => __( 'Roadtrips mit Kindern.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/roadtrip-de/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2022/12/trogir.jpg.webp',
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
			'title' => __( 'Nach Reiseart', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
