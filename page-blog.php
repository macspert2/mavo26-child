<?php
/**
 * Latest-posts "blog" index — plan-mid.md Phase 9. One shared template
 * for all 3 languages (FR /blog/, EN /en/blog/, DE /de/blog/ — all the
 * identical slug "blog", linked as Polylang translations of each other)
 * — the query/layout doesn't differ by language, only label text, same
 * reasoning as template-parts/mv-home/recent-posts.php.
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
	'posts_per_page'      => 12,
	'paged'               => $paged,
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
				$items = [];
				foreach ( $blog_query->posts as $blog_post ) {
					ob_start();
					get_template_part( 'template-parts/mv-shared/card-post', null, [
						'post' => $blog_post,
					] );
					$items[] = ob_get_clean();
				}
				get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
					'columns' => 3,
					'items'   => $items,
				] );
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
