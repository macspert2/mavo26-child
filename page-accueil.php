<?php
/**
 * French homepage template — promoted from prototype to the live front
 * page. Originally slug `accueil-prototype`, renamed to `accueil`; this
 * file was renamed to match, since the WordPress template hierarchy
 * matches by slug. Once assigned as the static front page in
 * Settings > Reading, this still applies via the normal page-template
 * fallback (front-page.php takes priority over page-{slug}.php, but
 * this theme has no front-page.php, so WordPress falls through to this
 * file) — no manual "Page Attributes > Template" assignment needed.
 *
 * File: page-accueil.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main mv-home">
	<?php
	// Section visibility is controlled from Réglages MaVo (wp-admin) —
	// see inc/mv-settings.php. Hero always shows; everything else is
	// toggleable. primary-pathways defaults off, matching its previous
	// hardcoded-disabled state, but can now be turned on without code.
	get_template_part( 'template-parts/mv-home/hero' );

	if ( mv_section_enabled( 'fr_trust_bar' ) ) {
		get_template_part( 'template-parts/mv-home/trust-bar' );
	}
	if ( mv_section_enabled( 'fr_primary_pathways' ) ) {
		get_template_part( 'template-parts/mv-home/primary-pathways' );
	}
	// Background rhythm per plan2.md §10.3 — subtle alternation, default
	// (white) on sections not explicitly called out there.
	if ( mv_section_enabled( 'fr_featured_destinations' ) ) {
		get_template_part( 'template-parts/mv-home/featured-destinations', null, [ 'background' => 'cream' ] );
	}
	if ( mv_section_enabled( 'fr_seasonal_guides' ) ) {
		get_template_part( 'template-parts/mv-home/seasonal-guides' );
	}
	if ( mv_section_enabled( 'fr_trip_type' ) ) {
		get_template_part( 'template-parts/mv-home/trip-type' );
	}
	if ( mv_section_enabled( 'fr_family_travel_themes' ) ) {
		get_template_part( 'template-parts/mv-home/family-travel-themes', null, [ 'background' => 'blue-tint' ] );
	}
	if ( mv_section_enabled( 'fr_popular_last_year' ) ) {
		get_template_part( 'template-parts/mv-home/popular-last-year' );
	}
	if ( mv_section_enabled( 'fr_recent_posts' ) ) {
		get_template_part( 'template-parts/mv-home/recent-posts' );
	}
	if ( mv_section_enabled( 'fr_about_mini' ) ) {
		get_template_part( 'template-parts/mv-home/about-mini', null, [ 'background' => 'cream' ] );
	}
	if ( mv_section_enabled( 'fr_start_here_cta' ) ) {
		get_template_part( 'template-parts/mv-home/start-here-cta' );
	}
	?>
</main>

<?php
get_footer();
