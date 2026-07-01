<?php
/**
 * Start Here — "Quel type de séjour ?" (plan-mid.md §5.2 group 3,
 * improved). The plan's draft list (Week-end, City-trip, Nature/rando,
 * Plage, Road trip, Train) mixes duration with activity type, and two of
 * its items (City-trip, Train) have no matching filter_slug. Split here:
 * this group uses the full real "interet" category (10 activity types);
 * duration gets its own group (duration.php) using the real "duree"
 * category instead.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-start-here-trip-type',
	'title'         => __( 'Quel type de séjour ?', 'mavo' ),
	'keys'          => [
		'plage_cote',
		'nature_rando',
		'gastronomie',
		'culture_histoire',
		'velo',
		'voile',
		'campervan',
		'ski',
		'activites_famille',
		'detente',
		'shopping',
		'roadtrip',
		'citytrip',
	],
	'columns'       => 4,
] );
