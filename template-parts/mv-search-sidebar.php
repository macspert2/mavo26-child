<?php
/**
 * Search results sidebar content — hooked via
 * generate_before_right_sidebar_content (functions.php), only on
 * is_search(). Custom content, not wp-admin-configured widgets.
 *
 * Markup matches GP's own generate_do_default_sidebar_widgets()
 * (<aside class="widget">, <h2 class="widget-title">) so it inherits
 * the theme's existing widget styling — no new CSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$strings = [
	'fr' => [
		'search_again' => 'Nouvelle recherche',
		'refine'       => 'Affiner par thème',
		'popular'      => 'Les plus lus',
		'latest'       => 'Derniers articles',
		'latest_link'  => 'Voir tous les articles',
	],
	'en' => [
		'search_again' => 'Search again',
		'refine'       => 'Browse by theme',
		'popular'      => 'Most read',
		'latest'       => 'Latest articles',
		'latest_link'  => 'See all articles',
	],
	'de' => [
		'search_again' => 'Neue Suche',
		'refine'       => 'Nach Thema',
		'popular'      => 'Meistgelesen',
		'latest'       => 'Neueste Artikel',
		'latest_link'  => 'Alle Artikel ansehen',
	],
];
$t = $strings[ $lang ] ?? $strings['fr'];

$focus_urls = [
	'fr' => 'https://www.mamanvoyage.com/nos-idees-de-voyage/',
	'en' => 'https://www.mamanvoyage.com/en/our-travel-ideas/',
	'de' => 'https://www.mamanvoyage.com/de/unsere-reiseideen/',
];
$focus_url = $focus_urls[ $lang ] ?? $focus_urls['fr'];

$blog_urls = [
	'fr' => 'https://www.mamanvoyage.com/blog/',
	'en' => 'https://www.mamanvoyage.com/en/blog-en/',
	'de' => 'https://www.mamanvoyage.com/de/blog-de/',
];
$blog_url = $blog_urls[ $lang ] ?? $blog_urls['fr'];
?>

<aside id="mv-search-again" class="widget widget_search">
	<h2 class="widget-title"><?php echo esc_html( $t['search_again'] ); ?></h2>
	<?php get_search_form(); ?>
</aside>

<?php if ( class_exists( 'TVF_Homepage' ) ) : ?>
<aside id="mv-search-refine" class="widget">
	<h2 class="widget-title"><?php echo esc_html( $t['refine'] ); ?></h2>
	<ul>
		<?php foreach ( [ 'bebe', 'jeunes_enfants', 'ados' ] as $key ) : ?>
			<?php
			$meta = TVF_Homepage::get_card_meta( $key, $lang );
			if ( ! $meta ) {
				continue;
			}
			$item_url = add_query_arg( 'f', implode( ',', $meta['slugs'] ), $focus_url );
			?>
			<li><a href="<?php echo esc_url( $item_url ); ?>"><?php echo esc_html( $meta['label'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</aside>
<?php endif; ?>

<?php if ( class_exists( 'TVF_Popular_Snapshots' ) ) : ?>
	<?php $popular_posts = TVF_Popular_Snapshots::get_most_viewed( $lang, 5 ); ?>
	<?php if ( ! empty( $popular_posts ) ) : ?>
	<aside id="mv-search-popular" class="widget">
		<h2 class="widget-title"><?php echo esc_html( $t['popular'] ); ?></h2>
		<ul>
			<?php foreach ( $popular_posts as $popular_post ) : ?>
				<li><a href="<?php echo esc_url( get_permalink( $popular_post ) ); ?>"><?php echo esc_html( get_the_title( $popular_post ) ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</aside>
	<?php endif; ?>
<?php endif; ?>

<aside id="mv-search-latest" class="widget">
	<h2 class="widget-title"><?php echo esc_html( $t['latest'] ); ?></h2>
	<p><a href="<?php echo esc_url( $blog_url ); ?>"><?php echo esc_html( $t['latest_link'] ); ?></a></p>
</aside>
