<?php
/**
 * English homepage template — promoted from prototype to the live front
 * page. Originally slug `home-prototype`, renamed to `home`; this file
 * was renamed to match, same mechanism as page-accueil.php. Linked as
 * the English translation of the French front page via Polylang, so it
 * appears at https://www.mamanvoyage.com/en/ once Settings > Reading
 * and Polylang's per-language static front page are both configured.
 *
 * File: page-home.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main mv-home">
	<?php
	// Section visibility is controlled from Réglages MaVo (wp-admin) —
	// see inc/mv-settings.php. Hero always shows.
	get_template_part( 'template-parts/mv-home-en/hero' );

	if ( mv_section_enabled( 'en_trust_bar' ) ) {
		get_template_part( 'template-parts/mv-home-en/trust-bar' );
	}
	// Background rhythm per plan2.md §10.3 — mirrors the FR homepage's
	// alternation for visual consistency across languages.
	if ( mv_section_enabled( 'en_destinations' ) ) {
		get_template_part( 'template-parts/mv-home-en/destinations', null, [ 'background' => 'cream' ] );
	}
	if ( mv_section_enabled( 'en_trip_type' ) ) {
		get_template_part( 'template-parts/mv-home-en/trip-type' );
	}
	if ( mv_section_enabled( 'en_family_travel_themes' ) ) {
		get_template_part( 'template-parts/mv-home-en/family-travel-themes', null, [ 'background' => 'blue-tint' ] );
	}
	if ( mv_section_enabled( 'en_recent_posts' ) ) {
		get_template_part( 'template-parts/mv-home/recent-posts' );
	}
	if ( mv_section_enabled( 'en_popular_last_year' ) ) {
		get_template_part( 'template-parts/mv-home/popular-last-year' );
	}
	if ( mv_section_enabled( 'en_about_mini' ) ) {
		get_template_part( 'template-parts/mv-home-en/about-mini', null, [ 'background' => 'cream' ] );
	}
	?>
</main>

<?php
get_footer();
