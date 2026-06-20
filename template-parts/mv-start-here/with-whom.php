<?php
/**
 * Start Here — "Je voyage avec..." (plan-mid.md §5.2 group 2). Maps
 * directly onto the full age_enfants category — no changes vs. the plan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-start-here-with-whom',
	'title'         => __( 'Je voyage avec...', 'mavo' ),
	'keys'          => [ 'bebe', 'jeunes_enfants', 'ados' ],
	'columns'       => 3,
] );
