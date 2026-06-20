<?php
/**
 * German homepage hero. Separate file from mv-home/hero.php on purpose —
 * see template-parts/mv-home-en/hero.php for the rationale. Draft copy
 * (translation, not native-reviewed) — please check tone/grammar before
 * launch.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mv-section mv-hero">
	<div class="mv-container mv-hero__inner">
		<h1 class="mv-hero__headline">
			<?php esc_html_e( 'Familienreisen durch Europa und die ganze Welt, seit 2009.', 'mavo' ); ?>
		</h1>
		<p class="mv-hero__promise">
			<?php esc_html_e( 'Hier teile ich unsere Reiserouten, unsere Lieblingsorte und familienerprobte Reisetipps für eure nächste Reise mit Kindern.', 'mavo' ); ?>
		</p>
		<div class="mv-hero__cta-row">
			<a class="mv-button mv-button--secondary" href="#mv-destinations">
				<?php esc_html_e( 'Reiseziele entdecken', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
