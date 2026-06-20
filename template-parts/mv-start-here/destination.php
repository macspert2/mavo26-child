<?php
/**
 * Start Here — "Je cherche une destination" (plan-mid.md §5.2 group 1,
 * adapted to the real geographie filter_slugs). Unlike the homepage's
 * featured-destinations tease, this shows all 6 — Start Here is allowed
 * to be exhaustive (plan §5.3).
 *
 * France and Angleterre have dedicated landing pages, so those two tiles
 * link there directly instead of the filtered [travel_finder_focus]
 * view the other four use.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-start-here-destination',
	'title'         => __( 'Je cherche une destination', 'mavo' ),
	'keys'          => [ 'france', 'angleterre', 'mediterranee', 'europe', 'sans_decalage', 'plus_loin' ],
	'columns'       => 3,
	'url_overrides' => [
		'france'     => 'https://www.mamanvoyage.com/france/',
		'angleterre' => 'https://www.mamanvoyage.com/angleterre/',
	],
] );
