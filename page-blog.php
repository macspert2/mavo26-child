<?php
/**
 * Latest-posts "blog" index — plan-mid.md Phase 9. One shared template
 * for all 3 languages (FR /blog/, EN /en/blog/, DE /de/blog/ — all the
 * identical slug "blog", linked as Polylang translations of each other)
 * — the query/layout doesn't differ by language, only label text, same
 * reasoning as template-parts/mv-home/recent-posts.php.
 *
 * Renders exactly like the site's current chronological homepage (`/`,
 * GeneratePress's own index.php) on request: each post via this child
 * theme's own content.php — full content up to any manual <!--more-->
 * tag, with the featured image and title, not a card grid. Reuses
 * content.php directly (not a re-implementation) for guaranteed visual
 * parity. posts_per_page matches the site's own "Posts per page" core
 * setting, so it shows the same count as the homepage automatically.
 *
 * content.php's post-divider logic reads the *global* $wp_query
 * ($wp_query->current_post/post_count), so the global is temporarily
 * swapped to this page's own query for the loop and restored via
 * wp_reset_query() afterward — otherwise that logic would silently look
 * at the wrong query object (this page's own singular main query).
 *
 * Deliberately NOT using WordPress's official "Posts page" Reading
 * setting — that's only assignable once "homepage displays" is also
 * switched to a static page, which hasn't happened yet. This is a plain
 * custom-templated page with its own WP_Query, fully decoupled from
 * that future switch.
 *
 * Pagination uses a `?paged=N` query string rather than the conventional
 * `/page/N/` rewrite — that rewrite is tied to the official Posts page/
 * wp_link_pages() mechanisms and isn't guaranteed to route correctly for
 * an arbitrary custom page template without touching rewrite rules.
 *
 * Matches slug `blog` via the WordPress template hierarchy, same
 * mechanism as page-accueil-prototype.php.
 *
 * File: page-blog.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$labels = [
	'fr' => [ 'older' => 'Articles précédents', 'newer' => 'Articles plus récents' ],
	'en' => [ 'older' => 'Older posts', 'newer' => 'Newer posts' ],
	'de' => [ 'older' => 'Ältere Beiträge', 'newer' => 'Neuere Beiträge' ],
];
$label = $labels[ $lang ] ?? $labels['fr'];

$paged = max( 1, (int) ( $_GET['paged'] ?? 1 ) );

$query_args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => get_option( 'posts_per_page' ),
	'paged'                => $paged,
	'ignore_sticky_posts' => true,
];

if ( function_exists( 'pll_current_language' ) ) {
	$query_args['lang'] = $lang;
}

$blog_query = new WP_Query( $query_args );
?>

<main id="primary" class="site-main mv-home mv-home--prototype">
	<section class="mv-section mv-blog-index">
		<div class="mv-container">
			<h1 class="mv-section__title"><?php echo esc_html( get_the_title() ); ?></h1>

			<?php if ( $blog_query->have_posts() ) : ?>
				<?php
				global $wp_query;
				$wp_query = $blog_query; // so content.php's $wp_query-based divider logic sees this loop, not the page's own singular query.

				while ( have_posts() ) :
					the_post();
					get_template_part( 'content', get_post_format() );
				endwhile;

				wp_reset_query(); // restores the global $wp_query and calls wp_reset_postdata().
				?>
				<nav class="mv-blog-index__pagination" aria-label="<?php esc_attr_e( 'Pagination', 'mavo' ); ?>">
					<?php if ( $paged > 1 ) : ?>
						<?php $prev_url = ( $paged - 1 ) > 1 ? add_query_arg( 'paged', $paged - 1 ) : remove_query_arg( 'paged' ); ?>
						<a class="mv-button mv-button--secondary" href="<?php echo esc_url( $prev_url ); ?>">
							<?php echo esc_html( $label['newer'] ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $paged < $blog_query->max_num_pages ) : ?>
						<a class="mv-button mv-button--secondary" href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>">
							<?php echo esc_html( $label['older'] ); ?>
						</a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
