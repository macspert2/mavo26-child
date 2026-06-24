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
		'description' => __( 'Week-ends, vacances, nature, plages, culture, vélo, van-life sans partir trop loin', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/france/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2018/01/IMG_0745.jpg',
	],
	[
		'title'       => __( 'Angleterre', 'mavo' ),
		'description' => __( 'Nos découvertes dans notre pays d'adoption : Londres, sud de l’Angleterre et autres escapades', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/angleterre/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2026/05/IMG_6132.jpeg.webp',
	],
	[
		'title'       => __( 'Italie', 'mavo' ),
		'description' => __( 'City-trips, îles, randonnées et road trips avec enfants.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/italie/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2010/03/26023_408646857888_508957888_4904843_5309085_n.jpg',
	],
	[
		'title'       => __( 'Espagne', 'mavo' ),
		'description' => __( 'Andalousie, Baléares, Canaries, Catalogne : culture, plages et randos en famille', 'mavo' ),
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

$background       = $args['background'] ?? '';
$section_classes   = 'mv-section mv-featured-destinations';
if ( $background ) {
	$section_classes .= ' mv-section--bg-' . sanitize_html_class( $background );
}
?>
<section class="<?php echo esc_attr( $section_classes ); ?>">
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
