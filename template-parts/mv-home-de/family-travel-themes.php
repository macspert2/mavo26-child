<?php
/**
 * German homepage family travel themes — mirrors mv-home/family-travel-themes.php.
 * Same rationale as template-parts/mv-home-en/family-travel-themes.php.
 * Links to the German [travel_finder_focus] page (catalog-tile-grid.php
 * resolves this automatically by language).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-family-travel-themes',
	'title'         => __( 'Reisen für jede Familie', 'mavo' ),
	'keys'          => function_exists( 'tvf_get_family_travel_theme_keys' ) ? tvf_get_family_travel_theme_keys() : [ 'bebe', 'jeunes_enfants', 'ados' ],
	'columns'       => 3,
] );
