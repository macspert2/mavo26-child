<?php
/**
 * Homepage Start Here CTA — Section 9 (plan-mid.md §4.1 / §4.3).
 *
 * Structure + the plan's drafted CTA copy (§4.3), now linking to the real
 * /commencez-ici/ page (plan Phase 5).
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
		<a class="mv-button mv-button--primary" href="https://www.mamanvoyage.com/commencez-ici/">
			<?php esc_html_e( 'Commencer ici', 'mavo' ); ?>
		</a>
	</div>
</section>
