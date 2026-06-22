<?php
/**
 * Homepage about mini — Section 8 (plan-mid.md §4.1).
 *
 * Copy per plan2.md §12.3 — names Christine, Ticoeur and Titpuce
 * explicitly (Ticoeur is a real, established nickname already used
 * elsewhere on the blog, e.g. post titles like "quand Ticoeur découvre
 * la magie du zoo"). Photo and About-page link are real.
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
				<?php echo esc_html( mv_get_string( 'about_title', 'fr' ) ); ?>
			</h2>
			<p>
				<?php echo esc_html( mv_get_string( 'about_text', 'fr' ) ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/a-propos/">
				<?php echo esc_html( mv_get_string( 'about_cta', 'fr' ) ); ?>
			</a>
		</div>
	</div>
</section>
