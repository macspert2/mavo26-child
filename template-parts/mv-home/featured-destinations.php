<?php
/**
 * Homepage featured destinations — Section 4 (plan-mid.md §4.1).
 *
 * Limited to the real geographie filter_slugs (france, angleterre,
 * mediterranee, europe, sans_decalage, plus_loin) — the plan's example
 * list (Italie, Espagne, Grèce, Portugal, Écosse, Croatie) doesn't match
 * the real registry; those would need new filter_slugs and data entry
 * before they could appear here.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-featured-destinations',
	'title'         => __( 'Destinations à la une', 'mavo' ),
	'keys'          => [ 'france', 'angleterre', 'mediterranee', 'europe' ],
	'columns'       => 4,
] );
