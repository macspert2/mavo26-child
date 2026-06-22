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
			<?php esc_html_e( 'Vous ne savez pas par où commencer ? Consultez notre guide pour trouver les meilleurs articles selon votre famille, votre destination et votre style de voyage.', 'mavo' ); ?>
		</p>
		<a class="mv-button mv-button--primary" href="https://www.mamanvoyage.com/explorer/">
			<?php esc_html_e( 'Explorer Maman Voyage', 'mavo' ); ?>
		</a>
	</div>
</section>
