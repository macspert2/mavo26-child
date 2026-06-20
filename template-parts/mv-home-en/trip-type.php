<?php
/**
 * English homepage trip-type tiles — hardcoded links to real WordPress
 * categories (not wp_tvf_post_filter-backed, same pattern as
 * destinations.php). English/German content uses categories like
 * city-trip/beach/hiking instead of the French filter table.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$placeholder_image = 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg';

$trip_types = [
	[
		'title'       => __( 'Hiking', 'mavo' ),
		'description' => __( 'Family hikes and nature trails.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/category/travel-with-kids/hiking/',
	],
	[
		'title'       => __( 'City breaks', 'mavo' ),
		'description' => __( 'City trips with the kids.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/category/travel-with-kids/citytrip-en/',
	],
	[
		'title'       => __( 'Beaches', 'mavo' ),
		'description' => __( 'Beach holidays with the family.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/category/travel-with-kids/beaches/',
	],
	[
		'title'       => __( 'Road trips', 'mavo' ),
		'description' => __( 'Road trips with kids.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/category/travel-with-kids/roadtrip-en/',
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
			'title' => __( 'Browse by trip type', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
