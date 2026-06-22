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
				<?php echo esc_html( mv_get_string( 'about_title', 'en' ) ); ?>
			</h2>
			<p>
				<?php echo esc_html( mv_get_string( 'about_text', 'en' ) ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/en/about/">
				<?php echo esc_html( mv_get_string( 'about_cta', 'en' ) ); ?>
			</a>
		</div>
	</div>
</section>
