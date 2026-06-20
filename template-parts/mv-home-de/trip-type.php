<?php
/**
 * German homepage trip-type tiles — same rationale as
 * template-parts/mv-home-en/trip-type.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$placeholder_image = 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg';

$trip_types = [
	[
		'title'       => __( 'Wandern', 'mavo' ),
		'description' => __( 'Wanderungen und Naturerlebnisse mit der Familie.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/wandern/',
	],
	[
		'title'       => __( 'Städtereisen', 'mavo' ),
		'description' => __( 'Städtetrips mit Kindern.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/staedtereise/',
	],
	[
		'title'       => __( 'Natur', 'mavo' ),
		'description' => __( 'Natur erleben mit der Familie.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/natur/',
	],
	[
		'title'       => __( 'Roadtrips', 'mavo' ),
		'description' => __( 'Roadtrips mit Kindern.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/category/reisen-mit-kindern/roadtrip-de/',
	],
];

$items = [];
foreach ( $trip_types as $trip_type ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $trip_type['url'],
		'title'       => $trip_type['title'],
		'description' => $trip_type['description'],
		'image'       => $placeholder_image,
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
