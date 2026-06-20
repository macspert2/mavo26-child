<?php
/**
 * Start Here — "Quel budget ?" (new group, not in the plan's draft — the
 * real "budget" filter category exists and is fully unused elsewhere, and
 * budget is a genuine, practical decision axis for "where should I
 * begin" — fits the page's own stated purpose).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-start-here-budget',
	'title'         => __( 'Quel budget ?', 'mavo' ),
	'keys'          => [ 'budget_eco', 'budget_medium', 'budget_premium' ],
	'columns'       => 3,
] );
