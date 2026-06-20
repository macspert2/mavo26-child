<?php
/**
 * English homepage hero. Separate file from mv-home/hero.php on purpose
 * (not a parameterized/shared template) — matches the project's chosen
 * approach of separate pages/content per language rather than one
 * language-switching template. Draft copy; swap before launch.
 *
 * No CTA button for the time being — removed on request, may come back
 * later. (Previously scrolled to the destinations tiles below via
 * #mv-destinations; that anchor ID is still on the destinations
 * section's wrapper if this gets restored.)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mv-section mv-hero">
	<div class="mv-container mv-hero__inner">
		<h1 class="mv-hero__headline">
			<?php esc_html_e( 'Family travel across Europe and around the world, since 2009.', 'mavo' ); ?>
		</h1>
		<p class="mv-hero__promise">
			<?php esc_html_e( 'I share our itineraries, our favourite places, and family-tested travel tips to help you plan your next trip with kids.', 'mavo' ); ?>
		</p>
	</div>
</section>
