<?php
/**
 * Homepage seasonal inspiration — Section 5 (plan-mid.md §4.1).
 *
 * Fixed to the 4 base seasons for now — no month-based rotation yet
 * (plan-mid.md §4.1 Section 5 mentions "plugin-driven by month" as an
 * option; deferred until the static list proves too limiting).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-seasonal-guides',
	'title'         => __( 'Idées de voyage selon la saison', 'mavo' ),
	'keys'          => [ 'printemps', 'ete', 'automne', 'hiver' ],
	'columns'       => 4,
] );
