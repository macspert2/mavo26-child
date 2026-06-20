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
	get_template_part( 'template-parts/mv-home-en/hero' );
	get_template_part( 'template-parts/mv-home-en/trust-bar' );
	get_template_part( 'template-parts/mv-home-en/destinations' );
	get_template_part( 'template-parts/mv-home-en/family-travel-themes' );
	get_template_part( 'template-parts/mv-home/recent-posts' );
	get_template_part( 'template-parts/mv-home/popular-last-year' );
	get_template_part( 'template-parts/mv-home-en/about-mini' );
	?>
</main>

<?php
get_footer();
