<?php
/**
 * German homepage about mini. Same real photo as the FR version.
 * Copy translated from plan2.md §12.3's FR version, for parity now that
 * the FR about-mini names Christine/Ticoeur/Titpuce explicitly rather
 * than staying generic. Not native-reviewed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about_image = 'https://www.mamanvoyage.com/wp-content/uploads/2018/01/3verres1bib_bannerIcon-1.jpg';
?>
<section class="mv-section mv-about-mini">
	<div class="mv-container mv-about-mini__inner">
		<span class="mv-about-mini__image">
			<img src="<?php echo esc_url( $about_image ); ?>" alt="" loading="lazy">
		</span>
		<div class="mv-about-mini__body">
			<h2 class="mv-section__title">
				<?php esc_html_e( 'Wer steckt hinter Maman Voyage?', 'mavo' ); ?>
			</h2>
			<p>
				<?php esc_html_e( 'Ich bin Christine, Mama von Ticoeur und Titpuce. Seit 2009 teile ich unsere Familienreisen: Wochenenden in der Nähe, Routen durch Europa, eine Weltreise, Wanderungen und Entdeckungen aus unserem Leben in England. Alle Tipps hier stammen aus echten Erfahrungen mit meinen Kindern.', 'mavo' ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/de/ueber-mich/">
				<?php esc_html_e( 'Mehr über uns erfahren', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
