<?php
/**
 * English homepage family travel themes — mirrors mv-home/family-travel-themes.php.
 * Possible now that mavo-travel-finder's wp_tvf_post_filter has English
 * rows (TVF_Store::sync_translations(), copied from the French
 * originals via Polylang translation links) and the catalog labels for
 * these 3 keys are translated (see homepage-catalog.php). Links to the
 * English [travel_finder_focus] page (catalog-tile-grid.php resolves
 * this automatically by language).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-family-travel-themes',
	'title'         => __( 'Travel with your family', 'mavo' ),
	'keys'          => function_exists( 'tvf_get_family_travel_theme_keys' ) ? tvf_get_family_travel_theme_keys() : [ 'bebe', 'jeunes_enfants', 'ados' ],
	'columns'       => 3,
	'background'    => $args['background'] ?? '',
] );
