<?php
/**
 * English homepage about mini. Same real photo as the FR version.
 * Copy translated from plan2.md §12.3's FR version, for parity now that
 * the FR about-mini names Christine/Ticoeur/Titpuce explicitly rather
 * than staying generic.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about_image = 'https://www.mamanvoyage.com/wp-content/uploads/2018/01/3verres1bib_bannerIcon-1.jpg';

$background      = $args['background'] ?? '';
$section_classes = 'mv-section mv-about-mini';
if ( $background ) {
	$section_classes .= ' mv-section--bg-' . sanitize_html_class( $background );
}
?>
<section class="<?php echo esc_attr( $section_classes ); ?>">
	<div class="mv-container mv-about-mini__inner">
		<span class="mv-about-mini__image">
			<img src="<?php echo esc_url( $about_image ); ?>" alt="" loading="lazy">
		</span>
		<div class="mv-about-mini__body">
			<h2 class="mv-section__title">
				<?php esc_html_e( "Who's behind Maman Voyage?", 'mavo' ); ?>
			</h2>
			<p>
				<?php esc_html_e( "I'm Christine, mum to Ticoeur and Titpuce. Since 2009, I've been sharing our family trips: weekends close to home, routes across Europe, a round-the-world trip, hikes, and discoveries from our life in England. Every tip published here comes from real experience with my own children.", 'mavo' ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/en/about/">
				<?php esc_html_e( 'Learn more about us', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
