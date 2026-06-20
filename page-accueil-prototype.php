<?php
/**
 * Hidden homepage prototype template.
 *
 * Matches slug `accueil-prototype` via the WordPress template hierarchy,
 * so no manual "Page Attributes > Template" assignment is required.
 *
 * File: page-accueil-prototype.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main mv-home mv-home--prototype">
	<?php
	get_template_part( 'template-parts/mv-home/hero' );
	get_template_part( 'template-parts/mv-home/trust-bar' );
	// primary-pathways ("Par où commencer ?") removed for the time being —
	// the template part itself is untouched, just not called here.
	get_template_part( 'template-parts/mv-home/featured-destinations' );
	get_template_part( 'template-parts/mv-home/seasonal-guides' );
	get_template_part( 'template-parts/mv-home/family-travel-themes' );
	get_template_part( 'template-parts/mv-home/popular-last-year' );
	get_template_part( 'template-parts/mv-home/recent-posts' );
	get_template_part( 'template-parts/mv-home/about-mini' );
	get_template_part( 'template-parts/mv-home/start-here-cta' );
	?>
</main>

<?php
get_footer();
