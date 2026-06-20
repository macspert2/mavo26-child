<?php
/**
 * German homepage about mini. Same real photo as the FR version;
 * translated copy is a draft (not native-reviewed).
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
				<?php esc_html_e( 'Ich bin mit meinen Kindern auf Weltreise gegangen und habe seitdem nie wirklich aufgehört zu reisen. Auf diesem Blog teile ich unsere Reiserouten, unsere Fehler und alles, was Familienreisen einfacher macht.', 'mavo' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Ob ihr ein Wochenende in der Nähe oder eine größere Reise plant – hier findet ihr hoffentlich alles, was ihr für euer nächstes Abenteuer braucht.', 'mavo' ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/de/ueber-mich/">
				<?php esc_html_e( 'Mehr über uns erfahren', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
