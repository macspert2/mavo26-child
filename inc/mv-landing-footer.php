<?php
/**
 * Simplified landing-page footer — plan2.md §5. The curated homepage
 * prototypes and Start Here page currently fall back into GP's normal
 * footer widget stack (full category lists, archive dropdown,
 * subscribe block, destinations list) once the curated content ends,
 * which undercuts the "modern landing page" feeling.
 *
 * Approach: unhook generate_construct_footer_widgets (GP's own widget-
 * area renderer, hooked to generate_footer at priority 5 — see GP's
 * inc/structure/footer.php) on these specific pages only, and hook a
 * much smaller replacement at the same priority instead. The copyright/
 * site-info bar (generate_construct_footer, priority 10) is untouched —
 * that's a normal, expected bit of footer, not "legacy clutter".
 *
 * Toggleable from Réglages MaVo (mv_section_enabled) in case this
 * doesn't look right in practice and needs reverting quickly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mv_is_landing_page(): bool {
	return is_page( [ 'accueil-prototype', 'home-prototype', 'startseite-prototyp', 'commencez-ici' ] );
}

add_action( 'wp', function () {
	if ( ! mv_is_landing_page() ) {
		return;
	}
	if ( ! mv_section_enabled( 'footer_simplified' ) ) {
		return;
	}
	remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
	add_action( 'generate_footer', 'mv_render_landing_footer', 5 );
} );

function mv_render_landing_footer(): void {
	$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

	$strings = [
		'fr' => [
			'tagline' => 'Voyages en famille, testés et racontés depuis 2009.',
			'about'   => 'À propos',
			'contact' => 'Contact',
			'legal'   => 'Mentions légales',
			'privacy' => 'Politique de confidentialité',
			'follow'  => 'Suivez-nous',
		],
		'en' => [
			'tagline' => 'Family trips, tested and told since 2009.',
			'about'   => 'About',
			'contact' => 'Contact',
			'legal'   => 'Legal notice',
			'privacy' => 'Privacy policy',
			'follow'  => 'Follow us',
		],
		'de' => [
			'tagline' => 'Familienreisen, erprobt und erzählt seit 2009.',
			'about'   => 'Über uns',
			'contact' => 'Kontakt',
			'legal'   => 'Impressum',
			'privacy' => 'Datenschutz',
			'follow'  => 'Folgt uns',
		],
	];
	$t = $strings[ $lang ] ?? $strings['fr'];

	$links = [
		'fr' => [
			'about'   => 'https://www.mamanvoyage.com/a-propos/',
			'contact' => 'https://www.mamanvoyage.com/a-propos/contactez-moi/',
			'legal'   => 'https://www.mamanvoyage.com/a-propos/mentions-legales/',
			'privacy' => 'https://www.mamanvoyage.com/a-propos/politique-de-confidentialite/',
		],
		'en' => [
			'about'   => 'https://www.mamanvoyage.com/en/about/',
			'contact' => 'https://www.mamanvoyage.com/a-propos/contactez-moi/',
			'legal'   => 'https://www.mamanvoyage.com/a-propos/mentions-legales/',
			'privacy' => 'https://www.mamanvoyage.com/a-propos/politique-de-confidentialite/',
		],
		'de' => [
			'about'   => 'https://www.mamanvoyage.com/de/ueber-mich/',
			'contact' => 'https://www.mamanvoyage.com/a-propos/contactez-moi/',
			'legal'   => 'https://www.mamanvoyage.com/a-propos/mentions-legales/',
			'privacy' => 'https://www.mamanvoyage.com/a-propos/politique-de-confidentialite/',
		],
	];
	$l = $links[ $lang ] ?? $links['fr'];
	?>
	<div class="footer-widgets mv-landing-footer">
		<div class="inside-footer-widgets mv-landing-footer__inner mv-container">
			<div class="mv-landing-footer__brand">
				<strong><?php esc_html_e( 'Maman Voyage', 'mavo' ); ?></strong>
				<p><?php echo esc_html( $t['tagline'] ); ?></p>
			</div>

			<nav class="mv-landing-footer__links" aria-label="<?php esc_attr_e( 'Liens utiles', 'mavo' ); ?>">
				<a href="<?php echo esc_url( $l['about'] ); ?>"><?php echo esc_html( $t['about'] ); ?></a>
				<a href="<?php echo esc_url( $l['contact'] ); ?>"><?php echo esc_html( $t['contact'] ); ?></a>
				<a href="<?php echo esc_url( $l['legal'] ); ?>"><?php echo esc_html( $t['legal'] ); ?></a>
				<a href="<?php echo esc_url( $l['privacy'] ); ?>"><?php echo esc_html( $t['privacy'] ); ?></a>
			</nav>

			<div class="mv-landing-footer__social">
				<span class="mv-landing-footer__social-label"><?php echo esc_html( $t['follow'] ); ?></span>
				<?php
				echo do_blocks(
					'<!-- wp:social-links -->
					<ul class="wp-block-social-links">
					<!-- wp:social-link {"url":"https://www.facebook.com/mamanvoyage","service":"facebook"} /-->
					<!-- wp:social-link {"url":"https://www.instagram.com/mamanvoyage/","service":"instagram"} /-->
					<!-- wp:social-link {"url":"https://www.pinterest.com/mamanvoyage/","service":"pinterest"} /-->
					</ul>
					<!-- /wp:social-links -->'
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output, not user input.
				?>
			</div>

			<?php if ( mv_section_enabled( 'footer_newsletter' ) ) : ?>
				<div class="mv-landing-footer__newsletter">
					<?php echo do_blocks( '<!-- wp:jetpack/subscriptions /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output, not user input. ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
