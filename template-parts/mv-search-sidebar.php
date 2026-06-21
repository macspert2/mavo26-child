<?php
/**
 * Search results sidebar content — hooked via
 * generate_before_right_sidebar_content (functions.php), only on
 * is_search(). Custom content, not wp-admin-configured widgets.
 *
 * Markup matches GP's own generate_do_default_sidebar_widgets()
 * (<aside class="widget">, <h2 class="widget-title">) so it inherits
 * the theme's existing widget styling. Cards reuse the same
 * card-link.php/card-post.php partials as the homepage, stacked
 * single-column (.mv-search-sidebar__cards in mv-home.css) rather than
 * through grid-wrapper.php, which clamps to 2-4 columns — too cramped
 * for a narrow sidebar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$strings = [
	'fr' => [
		'search_again' => 'Nouvelle recherche',
		'start_here'   => 'Vous ne savez pas par où commencer ?',
		'start_here_cta' => 'Commencez ici',
		'refine'       => 'Voyagez par thème',
		'popular'      => 'Les plus lus',
		'about'        => 'Qui se cache derrière Maman Voyage ?',
		'about_text'   => 'Je suis partie en tour du monde avec mes enfants, et je n’ai jamais vraiment arrêté de voyager depuis.',
		'about_cta'    => 'En savoir plus',
		'newsletter'   => 'Restez en contact',
		'follow'       => 'Suivez-nous',
		'latest'       => 'Derniers articles',
		'latest_link'  => 'Voir tous les articles',
	],
	'en' => [
		'search_again' => 'Search again',
		'refine'       => 'Travel by theme',
		'popular'      => 'Most read',
		'about'        => "Who's behind Maman Voyage?",
		'about_text'   => 'I set off on a world tour with my children, and I never really stopped travelling since.',
		'about_cta'    => 'Learn more',
		'newsletter'   => 'Stay in touch',
		'follow'       => 'Follow us',
		'latest'       => 'Latest articles',
		'latest_link'  => 'See all articles',
	],
	'de' => [
		'search_again' => 'Neue Suche',
		'refine'       => 'Reisen nach Thema',
		'popular'      => 'Meistgelesen',
		'about'        => 'Wer steckt hinter Maman Voyage?',
		'about_text'   => 'Ich bin mit meinen Kindern auf Weltreise gegangen und habe seitdem nie wirklich aufgehört zu reisen.',
		'about_cta'    => 'Mehr erfahren',
		'newsletter'   => 'Bleibt in Kontakt',
		'follow'       => 'Folgt uns',
		'latest'       => 'Neueste Artikel',
		'latest_link'  => 'Alle Artikel ansehen',
	],
];
$t = $strings[ $lang ] ?? $strings['fr'];

$about_urls = [
	'fr' => 'https://www.mamanvoyage.com/a-propos/',
	'en' => 'https://www.mamanvoyage.com/en/about/',
	'de' => 'https://www.mamanvoyage.com/de/ueber-mich/',
];
$about_url = $about_urls[ $lang ] ?? $about_urls['fr'];
$about_image = 'https://www.mamanvoyage.com/wp-content/uploads/2018/01/3verres1bib_bannerIcon-1.jpg';

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

$placeholder_image = mv_get_placeholder_image();
?>

<aside id="mv-search-again" class="widget widget_search">
	<h2 class="widget-title"><?php echo esc_html( $t['search_again'] ); ?></h2>
	<?php get_search_form(); ?>
</aside>

<?php if ( mv_section_enabled( 'sidebar_refine_theme' ) && class_exists( 'TVF_Homepage' ) ) : ?>
	<?php
	$theme_items = [];
	$used_images = [];
	foreach ( [ 'bebe', 'jeunes_enfants', 'ados' ] as $key ) {
		$meta = TVF_Homepage::get_card_meta( $key, $lang );
		if ( ! $meta ) {
			continue;
		}

		$posts = TVF_Homepage::get_card_posts( $key, $lang, 6 );
		$image = null;
		foreach ( $posts as $candidate ) {
			$candidate_image = get_the_post_thumbnail_url( $candidate, 'medium_large' );
			if ( $candidate_image && ! in_array( $candidate_image, $used_images, true ) ) {
				$image = $candidate_image;
				break;
			}
		}
		if ( ! $image && ! empty( $posts ) ) {
			$image = get_the_post_thumbnail_url( $posts[0], 'medium_large' );
		}
		if ( $image ) {
			$used_images[] = $image;
		} else {
			$image = $placeholder_image;
		}

		ob_start();
		get_template_part( 'template-parts/mv-shared/card-link', null, [
			'url'         => add_query_arg( 'f', implode( ',', $meta['slugs'] ), $focus_url ),
			'title'       => $meta['label'],
			'description' => $meta['description'],
			'image'       => $image,
			'variant'     => 'pathway',
		] );
		$theme_items[] = ob_get_clean();
	}
	?>
	<?php if ( ! empty( $theme_items ) ) : ?>
	<aside id="mv-search-refine" class="widget">
		<h2 class="widget-title"><?php echo esc_html( $t['refine'] ); ?></h2>
		<div class="mv-search-sidebar__cards">
			<?php foreach ( $theme_items as $theme_item ) { echo $theme_item; } ?>
		</div>
	</aside>
	<?php endif; ?>
<?php endif; ?>

<?php if ( mv_section_enabled( 'sidebar_most_read' ) && class_exists( 'TVF_Popular_Snapshots' ) ) : ?>
	<?php $popular_posts = TVF_Popular_Snapshots::get_most_viewed( $lang, mv_get_setting_count( 'sidebar_most_read_count', 4 ) ); ?>
	<?php if ( ! empty( $popular_posts ) ) : ?>
	<aside id="mv-search-popular" class="widget">
		<h2 class="widget-title"><?php echo esc_html( $t['popular'] ); ?></h2>
		<div class="mv-search-sidebar__cards">
			<?php foreach ( $popular_posts as $popular_post ) : ?>
				<?php
				get_template_part( 'template-parts/mv-shared/card-post', null, [
					'post'         => $popular_post,
					'show_excerpt' => false,
				] );
				?>
			<?php endforeach; ?>
		</div>
	</aside>
	<?php endif; ?>
<?php endif; ?>

<?php if ( mv_section_enabled( 'sidebar_about' ) ) : ?>
<aside id="mv-search-about" class="widget">
	<h2 class="widget-title"><?php echo esc_html( $t['about'] ); ?></h2>
	<p>
		<img class="mv-search-sidebar__about-image" src="<?php echo esc_url( $about_image ); ?>" alt="" loading="lazy">
		<?php echo esc_html( $t['about_text'] ); ?>
	</p>
	<p><a class="mv-button mv-button--secondary" href="<?php echo esc_url( $about_url ); ?>"><?php echo esc_html( $t['about_cta'] ); ?></a></p>
</aside>
<?php endif; ?>

<?php if ( mv_section_enabled( 'sidebar_newsletter' ) ) : ?>
<aside id="mv-search-newsletter" class="widget">
	<h2 class="widget-title"><?php echo esc_html( $t['newsletter'] ); ?></h2>
	<?php echo do_blocks( '<!-- wp:jetpack/subscriptions /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output, not user input. ?>
</aside>
<?php endif; ?>

<?php if ( mv_section_enabled( 'sidebar_social' ) ) : ?>
<aside id="mv-search-social" class="widget">
	<h2 class="widget-title"><?php echo esc_html( $t['follow'] ); ?></h2>
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
</aside>
<?php endif; ?>

<?php
$latest_query_args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => mv_get_setting_count( 'sidebar_latest_articles_count', 3 ),
	'ignore_sticky_posts' => true,
];
if ( function_exists( 'pll_current_language' ) ) {
	$latest_query_args['lang'] = $lang;
}
$latest_query = mv_section_enabled( 'sidebar_latest_articles' ) ? new WP_Query( $latest_query_args ) : null;
?>
<?php if ( $latest_query && $latest_query->have_posts() ) : ?>
<aside id="mv-search-latest" class="widget">
	<h2 class="widget-title"><?php echo esc_html( $t['latest'] ); ?></h2>
	<div class="mv-search-sidebar__cards">
		<?php foreach ( $latest_query->posts as $latest_post ) : ?>
			<?php
			get_template_part( 'template-parts/mv-shared/card-post', null, [
				'post'         => $latest_post,
				'show_excerpt' => false,
			] );
			?>
		<?php endforeach; ?>
	</div>
	<p class="mv-search-sidebar__more"><a href="<?php echo esc_url( $blog_url ); ?>"><?php echo esc_html( $t['latest_link'] ); ?></a></p>
</aside>
<?php endif; ?>

<?php
/**
 * Moved to the bottom of the sidebar on request — there's now also a
 * prominent "Commencez ici" orientation box at the top of the main
 * content area (inc/mv-search-page.php), so this stays far away from
 * it even on desktop's two-column layout, rather than sitting right
 * under the search box and effectively repeating the same CTA twice in
 * the same glance.
 */
?>
<?php if ( 'fr' === $lang && mv_section_enabled( 'sidebar_start_here' ) ) : ?>
<aside id="mv-search-start-here" class="widget">
	<h2 class="widget-title"><?php echo esc_html( $t['start_here'] ); ?></h2>
	<p><a class="mv-button mv-button--secondary" href="https://www.mamanvoyage.com/commencez-ici/"><?php echo esc_html( $t['start_here_cta'] ); ?></a></p>
</aside>
<?php endif; ?>
