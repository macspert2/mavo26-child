<?php
/**
 * Hidden English homepage prototype template.
 *
 * Matches slug `home-prototype` via the WordPress template hierarchy,
 * same mechanism as page-accueil-prototype.php. With Polylang, a page
 * using this template and linked as the English translation of the
 * (eventually promoted) French front page will appear at
 * https://www.mamanvoyage.com/en/ once Settings > Reading is configured
 * (verified live: /en/ already resolves today, to the default blog
 * index, confirming Polylang's URL structure works this way).
 *
 * File: page-home-prototype.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main mv-home mv-home--prototype">
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
