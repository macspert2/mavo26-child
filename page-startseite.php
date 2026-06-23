<?php
/**
 * German homepage template — promoted from prototype to the live front
 * page. Originally slug `startseite-prototyp`, renamed to `startseite`;
 * this file was renamed to match, same mechanism as page-accueil.php.
 * Linked as the German translation of the French front page via
 * Polylang, so it appears at https://www.mamanvoyage.com/de/ once
 * Settings > Reading and Polylang's per-language static front page are
 * both configured.
 *
 * File: page-startseite.php
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
	get_template_part( 'template-parts/mv-home-de/hero' );

	if ( mv_section_enabled( 'de_trust_bar' ) ) {
		get_template_part( 'template-parts/mv-home-de/trust-bar' );
	}
	// Background rhythm per plan2.md §10.3 — mirrors the FR homepage's
	// alternation for visual consistency across languages.
	if ( mv_section_enabled( 'de_destinations' ) ) {
		get_template_part( 'template-parts/mv-home-de/destinations', null, [ 'background' => 'cream' ] );
	}
	if ( mv_section_enabled( 'de_trip_type' ) ) {
		get_template_part( 'template-parts/mv-home-de/trip-type' );
	}
	if ( mv_section_enabled( 'de_family_travel_themes' ) ) {
		get_template_part( 'template-parts/mv-home-de/family-travel-themes', null, [ 'background' => 'blue-tint' ] );
	}
	if ( mv_section_enabled( 'de_recent_posts' ) ) {
		get_template_part( 'template-parts/mv-home/recent-posts' );
	}
	if ( mv_section_enabled( 'de_popular_last_year' ) ) {
		get_template_part( 'template-parts/mv-home/popular-last-year' );
	}
	if ( mv_section_enabled( 'de_about_mini' ) ) {
		get_template_part( 'template-parts/mv-home-de/about-mini', null, [ 'background' => 'cream' ] );
	}
	?>
</main>

<?php
get_footer();
