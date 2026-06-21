<?php
/**
 * Hidden German homepage prototype template.
 *
 * Matches slug `startseite-prototyp` via the WordPress template
 * hierarchy, same mechanism as page-accueil-prototype.php. With
 * Polylang, a page using this template and linked as the German
 * translation of the (eventually promoted) French front page will
 * appear at https://www.mamanvoyage.com/de/ once Settings > Reading is
 * configured (verified live: /de/ already resolves today, to the
 * default blog index, confirming Polylang's URL structure works this
 * way).
 *
 * File: page-startseite-prototyp.php
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
	get_template_part( 'template-parts/mv-home-de/hero' );

	if ( mv_section_enabled( 'de_trust_bar' ) ) {
		get_template_part( 'template-parts/mv-home-de/trust-bar' );
	}
	if ( mv_section_enabled( 'de_destinations' ) ) {
		get_template_part( 'template-parts/mv-home-de/destinations' );
	}
	if ( mv_section_enabled( 'de_trip_type' ) ) {
		get_template_part( 'template-parts/mv-home-de/trip-type' );
	}
	if ( mv_section_enabled( 'de_family_travel_themes' ) ) {
		get_template_part( 'template-parts/mv-home-de/family-travel-themes' );
	}
	if ( mv_section_enabled( 'de_recent_posts' ) ) {
		get_template_part( 'template-parts/mv-home/recent-posts' );
	}
	if ( mv_section_enabled( 'de_popular_last_year' ) ) {
		get_template_part( 'template-parts/mv-home/popular-last-year' );
	}
	if ( mv_section_enabled( 'de_about_mini' ) ) {
		get_template_part( 'template-parts/mv-home-de/about-mini' );
	}
	?>
</main>

<?php
get_footer();
