<?php
/**
 * Latest-posts "blog" index — plan-mid.md Phase 9. Mirrors
 * GeneratePress's own index.php structure/hooks/attrs exactly
 * (generate_do_attr(), generate_has_default_loop(),
 * generate_do_template_part('index') per post, generate_construct_sidebars())
 * instead of reinventing any of it, so this renders identically to the
 * site's current chronological homepage (/) using the theme's existing
 * CSS — no new CSS needed.
 *
 * One shared template for all 3 languages — FR /blog/ (slug `blog`,
 * matched directly), EN /en/blog/ and DE /de/blog/ (slugs `blog-en`/
 * `blog-de` — Polylang wouldn't allow reusing the identical slug
 * `blog` across languages here, unlike other pages in this project, so
 * page-blog-en.php/page-blog-de.php just require this file directly).
 * The query doesn't differ by language.
 *
 * The only addition vs. index.php: a custom WP_Query (latest posts,
 * language-filtered, posts_per_page matching the site's own "Posts per
 * page" setting), temporarily swapped into the global $wp_query before
 * the loop and restored via wp_reset_query() after — since on a *page*
 * template, the natural main query is just this single page, not a
 * list of posts, and have_posts()/the_post() (used inside
 * generate_do_template_part()) operate on that global.
 *
 * is_home is also force-set true on that query. GeneratePress's own
 * pagination (generate_content_nav(), hooked to generate_after_loop via
 * generate_do_post_navigation) only renders prev/next links when
 * is_home()||is_archive()||is_search() — conditional tags that read
 * whatever $wp_query currently points at, not a fixed snapshot of the
 * real request. A freshly-built WP_Query for post_type=post doesn't get
 * flagged as any of those on its own, so without this, GP's pagination
 * silently no-ops on a page template even though max_num_pages is set
 * correctly.
 *
 * Deliberately NOT using WordPress's official "Posts page" Reading
 * setting, even now that "homepage displays" is a static page and the
 * dropdown is assignable — that setting routes through the home.php/
 * index.php template hierarchy, completely bypassing this page's own
 * page-{slug}.php template (the same kind of slug-vs-front-page
 * gotcha as page-accueil.php itself), which would silently undo
 * everything this file does. Leave "Posts page" unset; this page's own
 * slug-matched template is what actually serves /blog/.
 *
 * Matches slug `blog` via the WordPress template hierarchy, same
 * mechanism as page-accueil.php.
 *
 * The home/blog body classes are added below via the body_class filter
 * (registered before get_header() so it's in place when the body tag
 * prints). WordPress sets those classes from is_home(), which is
 * decided before this template's later $wp_query swap even runs — so
 * without this, GeneratePress's own .home/.blog-scoped CSS (e.g.
 * `body.home h2.entry-title{font-size:40px;}`) never matches here, even
 * though the visual content is otherwise identical to the homepage.
 *
 * File: page-blog.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'home';
	$classes[] = 'blog';
	return $classes;
} );

get_header();

$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$query_args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => get_option( 'posts_per_page' ),
	'paged'               => max( 1, (int) ( $_GET['paged'] ?? 1 ) ),
	'ignore_sticky_posts' => true,
];

if ( function_exists( 'pll_current_language' ) ) {
	$query_args['lang'] = $lang;
}

global $wp_query;
$wp_query = new WP_Query( $query_args );
$wp_query->is_home = true;
?>

	<div <?php generate_do_attr( 'content' ); ?>>
		<main <?php generate_do_attr( 'main' ); ?>>
			<?php
			do_action( 'generate_before_main_content' );

			if ( generate_has_default_loop() ) {
				if ( have_posts() ) :

					do_action( 'generate_before_loop', 'index' );

					while ( have_posts() ) :

						the_post();

						generate_do_template_part( 'index' );

					endwhile;

					do_action( 'generate_after_loop', 'index' );

				else :

					generate_do_template_part( 'none' );

				endif;
			}

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	wp_reset_query();

	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
