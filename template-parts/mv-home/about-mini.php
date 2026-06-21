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
?>
<section class="mv-section mv-about-mini">
	<div class="mv-container mv-about-mini__inner">
		<span class="mv-about-mini__image">
			<img src="<?php echo esc_url( $about_image ); ?>" alt="" loading="lazy">
		</span>
		<div class="mv-about-mini__body">
			<h2 class="mv-section__title">
				<?php esc_html_e( 'Qui se cache derrière Maman Voyage ?', 'mavo' ); ?>
			</h2>
			<p>
				<?php esc_html_e( 'Je suis Christine, maman de Ticoeur et Titpuce. Depuis 2009, je partage nos voyages en famille : week-ends près de chez nous, itinéraires en Europe, tour du monde, randonnées et découvertes depuis notre vie en Angleterre. Tous les conseils publiés ici viennent d’expériences vécues avec mes enfants.', 'mavo' ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/a-propos/">
				<?php esc_html_e( 'En savoir plus sur nous', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
