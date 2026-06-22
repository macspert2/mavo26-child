<?php
/**
 * Homepage hero — Section 1 (plan-mid.md §4.1).
 *
 * Copy per plan2.md §6.2 (shorter version, chosen over the longer
 * personal/storytelling alternative).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mv-section mv-hero">
	<div class="mv-container mv-hero__inner">
		<h1 class="mv-hero__headline">
			<?php echo esc_html( mv_get_string( 'hero_headline', 'fr' ) ); ?>
		</h1>
		<p class="mv-hero__promise">
			<?php echo esc_html( mv_get_string( 'hero_promise', 'fr' ) ); ?>
		</p>
		<div class="mv-hero__cta-row">
			<a class="mv-button mv-button--primary" href="https://www.mamanvoyage.com/ou-partir-trouvez-votre-prochain-voyage/">
				<?php esc_html_e( 'Trouvez une idée de voyage', 'mavo' ); ?>
			</a>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/nos-voyages/destinations/">
				<?php esc_html_e( 'Explorez les destinations', 'mavo' ); ?>
			</a>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/commencez-ici/">
				<?php esc_html_e( 'Commencez ici', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
