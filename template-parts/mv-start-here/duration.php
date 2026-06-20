<?php
/**
 * Start Here — "Combien de temps ?" (new group, not in the plan's draft —
 * split out of its "type de voyage" group since duration is a distinct
 * decision axis from activity type, and matches the real "duree" filter
 * category exactly).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-start-here-duration',
	'title'         => __( 'Combien de temps ?', 'mavo' ),
	'keys'          => [ 'weekend', 'une_semaine', 'long_sejour' ],
	'columns'       => 3,
] );
