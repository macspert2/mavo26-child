<?php
/**
 * Geo tag → landing page: the link itself, read by everything.
 *
 * Kept apart from mv-geo-hub-admin.php because that file only loads in the
 * admin, while the two consumers — the geo badges and the breadcrumb filter
 * below — both run on the front end.
 *
 * The mapping is stored as term meta by the tag edit screen; see
 * inc/mv-geo-hub-admin.php.
 */

defined( 'ABSPATH' ) || exit;


/**
 * The landing page a geo tag is linked to, or 0.
 *
 * The meta key was read raw in three places before this existed. It is the kind
 * of string that gets mistyped once and then quietly returns nothing.
 */
function mv_geo_hub_page_id( int $term_id ): int {
	return $term_id > 0 ? (int) get_term_meta( $term_id, '_mv_hub_page_id', true ) : 0;
}

/** The landing page's URL, or '' when the tag has none. */
function mv_geo_hub_url( int $term_id ): string {
	$page_id = mv_geo_hub_page_id( $term_id );

	if ( ! $page_id ) {
		return '';
	}

	$url = get_permalink( $page_id );

	return is_string( $url ) ? $url : '';
}

/**
 * Points geo breadcrumbs at the landing page, the way geo badges already are.
 *
 * Mavo Geotag Plus builds the breadcrumb and asks, through this filter, where
 * each crumb should go; it deliberately does not know this meta key. So a
 * reader gets /europe/ rather than /tag/europe/ in the visible crumb and in the
 * JSON-LD — which is also the page we would rather Google treated as canonical
 * for the place.
 *
 * Tags without a landing page fall through untouched to the tag archive.
 */
add_filter( 'mavo_geo_term_url', 'mv_geo_hub_breadcrumb_url', 10, 2 );
function mv_geo_hub_breadcrumb_url( $url, int $term_id ) {
	$hub_url = mv_geo_hub_url( $term_id );

	return $hub_url !== '' ? $hub_url : $url;
}
