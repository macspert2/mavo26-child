<?php
/**
 * English homepage hero. Separate file from mv-home/hero.php on purpose
 * (not a parameterized/shared template) — matches the project's chosen
 * approach of separate pages/content per language rather than one
 * language-switching template.
 *
 * No CTA button for the time being — removed on request, may come back
 * later. (Previously scrolled to the destinations tiles below via
 * #mv-destinations; that anchor ID is still on the destinations
 * section's wrapper if this gets restored.)
 *
 * Copy per plan2.md §6.3 (shorter version, chosen over the longer
 * personal/storytelling alternative).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mv-section mv-hero">
	<div class="mv-container mv-hero__inner">
		<h1 class="mv-hero__headline">
			<?php echo esc_html( mv_get_string( 'hero_headline', 'en' ) ); ?>
		</h1>
		<p class="mv-hero__promise">
			<?php echo esc_html( mv_get_string( 'hero_promise', 'en' ) ); ?>
		</p>
	</div>
</section>
