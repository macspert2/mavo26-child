<?php
/**
 * English homepage destination tiles — hardcoded, not catalog/
 * wp_tvf_post_filter-backed (no English filter data exists). Links go
 * to real tag archives. Order/selection per plan2.md §14.4's priority
 * list (UK, France, Italy, Spain, Greece, ...) — capped at 4 to stay
 * selective (plan2.md §4.1), so Greece (8 posts, lowest of the
 * original top 4) was dropped in favour of France (5 posts) to give
 * the homepage its own French-perspective angle, per §14.3. Each tile
 * has its own real photo; France reuses the FR homepage's own France
 * photo (featured-destinations.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$destinations = [
	[
		'title'       => __( 'United Kingdom', 'mavo' ),
		'description' => __( 'Family days out, train trips, London ideas and southern England discoveries from our expat life.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/united-kingdom-en/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2019/05/P4200473.jpeg.webp',
	],
	[
		'title'       => __( 'France', 'mavo' ),
		'description' => __( 'French family travel ideas from weekends close to home to longer holiday routes.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/france-en/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2018/01/IMG_0745.jpg',
	],
	[
		'title'       => __( 'Italy', 'mavo' ),
		'description' => __( 'City breaks, islands, food, hikes and family-friendly road trips.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/italy/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2016/11/DSCF0361.jpg.webp',
	],
	[
		'title'       => __( 'Spain', 'mavo' ),
		'description' => __( 'Sun, islands, cities and easy family escapes tested with children.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/en/tag/spain/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2019/10/P9031799.jpeg.webp',
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

$background      = $args['background'] ?? '';
$section_classes = 'mv-section mv-featured-destinations';
if ( $background ) {
	$section_classes .= ' mv-section--bg-' . sanitize_html_class( $background );
}
?>
<section class="<?php echo esc_attr( $section_classes ); ?>" id="mv-destinations">
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
