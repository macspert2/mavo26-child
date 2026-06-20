<?php
/**
 * Start Here intro — page H1 + short framing line.
 *
 * page-commencez-ici.php is a fully custom template (get_header() +
 * template parts, no content-page.php in the loop), so the WordPress
 * page title never renders on its own — same situation as
 * page-accueil-prototype.php, where hero.php's <h1> is the only one.
 * This is that page's missing H1, using the real page title so it stays
 * correct if the page is ever renamed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mv-section mv-start-here-intro">
	<div class="mv-container">
		<h1 class="mv-start-here-intro__title"><?php echo esc_html( get_the_title() ); ?></h1>
		<p>
			<?php esc_html_e( 'Choisissez une destination, l’âge de vos enfants ou le type de séjour qui vous correspond : voici nos meilleures idées selon votre situation.', 'mavo' ); ?>
		</p>
	</div>
</section>
