<?php
/**
 * English homepage hero. Separate file from mv-home/hero.php on purpose
 * (not a parameterized/shared template) — matches the project's chosen
 * approach of separate pages/content per language rather than one
 * language-switching template. Draft copy; swap before launch.
 *
 * Single CTA only (vs. FR's 3) — there's no travel-finder/focus-page or
 * Start Here equivalent for English content yet, so it just scrolls to
 * the destinations tiles below.
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
		<div class="mv-hero__cta-row">
			<a class="mv-button mv-button--secondary" href="#mv-destinations">
				<?php esc_html_e( 'Explore destinations', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
