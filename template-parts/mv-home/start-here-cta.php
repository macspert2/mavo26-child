<?php
/**
 * Homepage Start Here CTA — Section 9 (plan-mid.md §4.1 / §4.3).
 *
 * Structure + the plan's drafted CTA copy (§4.3), now linking to the real
 * /explorer/ page (plan Phase 5; renamed from /commencez-ici/ — "Explorer
 * Maman Voyage", an in-between step from the homepage to the more
 * technical "où partir" page).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mv-section mv-start-here-cta">
	<div class="mv-container mv-start-here-cta__inner">
		<p class="mv-start-here-cta__text">
			<?php esc_html_e( 'Besoin d'inspiration ? Consultez nos voyages par destination, par type, par saison, par durée, selon l'âge de vos enfants et selon votre budget.', 'mavo' ); ?>
		</p>
		<a class="mv-button mv-button--primary" href="https://www.mamanvoyage.com/explorer/">
			<?php esc_html_e( 'Explorez nos voyages par catégorie', 'mavo' ); ?>
		</a>
	</div>
</section>
