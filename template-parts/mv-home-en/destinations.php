<?php
/**
 * English homepage destination tiles — hardcoded, not catalog/
 * wp_tvf_post_filter-backed (no English filter data exists). Links go
 * to real tag archives, verified live: United Kingdom (18 posts),
 * Spain (13), Italy (11), Greece (8) — the top 4 by post count, same
 * "hardcode the dominant few" approach as the FR homepage's
 * featured-destinations.php. All 4 still share the generic placeholder
 * photo — no per-destination images picked yet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$placeholder_image = 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg';

$destinations = [
	[
		'title'       => __( 'United Kingdom', 'mavo' ),
		'description' => __( 'Family life and travel in the UK.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/united-kingdom-en/',
	],
	[
		'title'       => __( 'Spain', 'mavo' ),
		'description' => __( 'Family trips around Spain.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/spain/',
	],
	[
		'title'       => __( 'Italy', 'mavo' ),
		'description' => __( 'Family trips around Italy.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/italy/',
	],
	[
		'title'       => __( 'Greece', 'mavo' ),
		'description' => __( 'Family trips around Greece.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/greece/',
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
<section class="mv-section mv-featured-destinations" id="mv-destinations">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Featured destinations', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
