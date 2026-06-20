<?php
/**
 * Homepage family travel themes — Section 6 (plan-mid.md §4.1).
 *
 * Backed by mavo-travel-finder's homepage catalog (see
 * includes/homepage-catalog.php in that plugin). Keys are the full
 * age_enfants set (bébé/jeunes enfants/ados) — chosen specifically to
 * match the "selon votre famille" framing; previously included
 * duration/activity tiles (une_semaine, nature_rando, plage_cote) that
 * didn't actually fit that title. Swap the `keys` list any time; no
 * plugin changes needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
	'section_class' => 'mv-family-travel-themes',
	'title'         => __( 'Voyager selon votre famille', 'mavo' ),
	'keys'          => [ 'bebe', 'jeunes_enfants', 'ados' ],
	'columns'       => 3,
] );
