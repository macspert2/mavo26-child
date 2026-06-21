<?php
/**
 * German homepage hero. Separate file from mv-home/hero.php on purpose —
 * see template-parts/mv-home-en/hero.php for the rationale. Draft copy
 * (translation, not native-reviewed) — please check tone/grammar before
 * launch.
 *
 * No CTA button for the time being — removed on request, may come back
 * later. (Previously scrolled to the destinations tiles below via
 * #mv-destinations; that anchor ID is still on the destinations
 * section's wrapper if this gets restored.)
 *
 * Copy per plan2.md §6.4 (shorter version, chosen over the longer
 * personal/storytelling alternative).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mv-section mv-hero">
	<div class="mv-container mv-hero__inner">
		<h1 class="mv-hero__headline">
			<?php esc_html_e( 'Erprobte Familienreisen in Europa und rund um die Welt.', 'mavo' ); ?>
		</h1>
		<p class="mv-hero__promise">
			<?php esc_html_e( 'Seit 2009 teile ich auf Maman Voyage unsere Reiserouten, Lieblingsorte und praktischen Tipps für entspanntere Reisen mit Kindern.', 'mavo' ); ?>
		</p>
	</div>
</section>
