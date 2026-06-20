<?php
/**
 * Homepage about mini — Section 8 (plan-mid.md §4.1).
 *
 * Draft editorial copy; swap before launch. Photo and About-page link
 * are real.
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
				<?php esc_html_e( 'Je suis partie en tour du monde avec mes enfants, et je n’ai jamais vraiment arrêté de voyager depuis. Sur ce blog, je partage nos itinéraires, nos erreurs et tout ce qui rend les voyages en famille plus simples.', 'mavo' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Que vous partiez pour un week-end pas loin de chez vous ou pour un grand voyage, j’espère que vous trouverez ici de quoi préparer votre prochaine aventure.', 'mavo' ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/a-propos/">
				<?php esc_html_e( 'En savoir plus sur nous', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
