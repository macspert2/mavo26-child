<?php
/**
 * English homepage about mini. Same real photo as the FR version;
 * translated copy is a draft.
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
				<?php esc_html_e( "Who's behind Maman Voyage?", 'mavo' ); ?>
			</h2>
			<p>
				<?php esc_html_e( 'I set off on a world tour with my children, and I never really stopped travelling since. On this blog, I share our itineraries, our mistakes, and everything that makes family travel easier.', 'mavo' ); ?>
			</p>
			<p>
				<?php esc_html_e( "Whether you're planning a weekend close to home or a bigger trip abroad, I hope you'll find what you need here to plan your next adventure.", 'mavo' ); ?>
			</p>
			<a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/en/about/">
				<?php esc_html_e( 'Learn more about us', 'mavo' ); ?>
			</a>
		</div>
	</div>
</section>
