<?php
/**
 * Start Here page template (plan-mid.md Phase 5).
 *
 * Matches slug `commencez-ici` via the WordPress template hierarchy, same
 * mechanism as page-accueil-prototype.php — no manual "Page Attributes >
 * Template" assignment required. The WordPress page title ("Commencez
 * ici") renders as the page's own H1 via content-page.php; every group
 * below uses an H2, set by mv-shared/section-header.php's default.
 *
 * File: page-commencez-ici.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main mv-start-here">
	<?php
	get_template_part( 'template-parts/mv-start-here/intro' );
	get_template_part( 'template-parts/mv-start-here/destination' );
	get_template_part( 'template-parts/mv-start-here/with-whom' );
	get_template_part( 'template-parts/mv-start-here/trip-type' );
	get_template_part( 'template-parts/mv-start-here/duration' );
	get_template_part( 'template-parts/mv-start-here/budget' );
	get_template_part( 'template-parts/mv-start-here/big-projects' );
	get_template_part( 'template-parts/mv-start-here/best-articles' );
	?>
</main>

<?php
get_footer();
