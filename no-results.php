<?php
/**
 * No-results state — overrides GeneratePress's own no-results.php
 * (root of the parent theme). GP's version has no hook inside its
 * is_search() branch (the message/search-form there is hardcoded
 * inline, unlike most of GP's other templates), so there's no way to
 * customize just that branch without overriding the whole file. The
 * is_home() and generic-404 branches below are otherwise byte-for-byte
 * identical to GP's own — only the is_search() branch changed, per
 * plan3.md §11.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mv_lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$mv_strings = [
	'fr' => [
		'title'       => 'Aucun article trouvé pour «%s».',
		'suggestions' => 'Essayez avec un pays, une ville ou un type de voyage : France, Angleterre, Italie, randonnée, bébé, ados, road trip…',
	],
	'en' => [
		'title'       => 'No articles found for “%s”.',
		'suggestions' => 'Try searching for a country, city or type of trip: France, England, Italy, hiking, baby, teens, road trip…',
	],
	'de' => [
		'title'       => 'Keine Artikel für „%s“ gefunden.',
		'suggestions' => 'Versucht es mit einem Land, einer Stadt oder einer Reiseart: Frankreich, England, Italien, Wandern, Baby, Teenager, Roadtrip…',
	],
];
$mv_t = $mv_strings[ $mv_lang ] ?? $mv_strings['fr'];

// FR has a real Start Here page and destinations hub; EN/DE have
// neither, so they get a single "back to homepage" button instead of
// 3 buttons pointing at pages that don't exist for them.
$mv_buttons = [
	'fr' => [
		[ 'label' => 'Explorer Maman Voyage', 'url' => 'https://www.mamanvoyage.com/explorer/' ],
		[ 'label' => 'Toutes nos destinations', 'url' => 'https://www.mamanvoyage.com/nos-voyages/destinations/' ],
		[ 'label' => 'Retour à l’accueil', 'url' => 'https://www.mamanvoyage.com/' ],
	],
	'en' => [
		[ 'label' => 'Back to homepage', 'url' => 'https://www.mamanvoyage.com/en/' ],
	],
	'de' => [
		[ 'label' => 'Zur Startseite', 'url' => 'https://www.mamanvoyage.com/de/' ],
	],
];
$mv_page_buttons = $mv_buttons[ $mv_lang ] ?? $mv_buttons['fr'];
?>
<div class="no-results not-found">
	<div class="inside-article">
		<?php
		/**
		 * generate_before_content hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_featured_page_header_inside_single - 10
		 */
		do_action( 'generate_before_content' );
		?>
		<header <?php generate_do_attr( 'entry-header' ); ?>>
			<h1 class="entry-title">
				<?php
				if ( is_search() ) {
					echo esc_html( sprintf( $mv_t['title'], get_search_query() ) );
				} else {
					esc_html_e( 'Nothing Found', 'generatepress' );
				}
				?>
			</h1>
		</header>
		<?php
		/**
		 * generate_after_entry_header hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_post_image - 10
		 */
		do_action( 'generate_after_entry_header' );
		?>
		<div class="entry-content">
				<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

					<p>
						<?php
						printf(
							/* translators: 1: Admin URL */
							esc_html__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'generatepress' ),
							esc_url( admin_url( 'post-new.php' ) )
						);
						?>
					</p>

				<?php elseif ( is_search() ) : ?>

					<p><?php echo esc_html( $mv_t['suggestions'] ); ?></p>
					<?php get_search_form(); ?>
					<div class="mv-no-results__buttons">
						<?php foreach ( $mv_page_buttons as $mv_button ) : ?>
							<a class="mv-button mv-button--secondary" href="<?php echo esc_url( $mv_button['url'] ); ?>"><?php echo esc_html( $mv_button['label'] ); ?></a>
						<?php endforeach; ?>
					</div>

				<?php else : ?>

					<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'generatepress' ); ?></p>
					<?php get_search_form(); ?>

				<?php endif; ?>
		</div>
		<?php
		/**
		 * generate_after_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'generate_after_content' );
		?>
	</div>
</div>
