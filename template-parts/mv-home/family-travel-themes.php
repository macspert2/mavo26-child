<?php
/**
 * Homepage family travel themes — Section 6 (plan-mid.md §4.1).
 *
 * Backed by mavo-travel-finder's homepage catalog (see
 * includes/homepage-catalog.php in that plugin). Which keys show is now
 * configurable from the plugin's Réglages admin page
 * (tvf_get_family_travel_theme_keys(), option
 * tvf_family_travel_theme_keys) rather than hardcoded here — defaults
 * to bebe/jeunes_enfants/ados (the full age_enfants set, matching the
 * "selon votre famille" framing) if unset.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-family-travel-themes',
	'title'         => __( 'Voyager selon votre famille', 'mavo' ),
	'keys'          => function_exists( 'tvf_get_family_travel_theme_keys' ) ? tvf_get_family_travel_theme_keys() : [ 'bebe', 'jeunes_enfants', 'ados' ],
	'columns'       => 3,
	'background'    => $args['background'] ?? '',
] );
