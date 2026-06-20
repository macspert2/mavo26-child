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
	get_template_part( 'template-parts/mv-home-de/hero' );
	get_template_part( 'template-parts/mv-home-de/trust-bar' );
	get_template_part( 'template-parts/mv-home-de/destinations' );
	get_template_part( 'template-parts/mv-home-de/family-travel-themes' );
	get_template_part( 'template-parts/mv-home/recent-posts' );
	get_template_part( 'template-parts/mv-home/popular-last-year' );
	get_template_part( 'template-parts/mv-home-de/about-mini' );
	?>
</main>

<?php
get_footer();
