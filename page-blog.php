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
 * One shared template for all 3 languages (FR /blog/, EN /en/blog/, DE
 * /de/blog/ — all the identical slug "blog", linked as Polylang
 * translations of each other) — the query doesn't differ by language.
 *
 * The only addition vs. index.php: a custom WP_Query (latest posts,
 * language-filtered, posts_per_page matching the site's own "Posts per
 * page" setting), temporarily swapped into the global $wp_query before
 * the loop and restored via wp_reset_query() after — since on a *page*
 * template, the natural main query is just this single page, not a
 * list of posts, and have_posts()/the_post() (used inside
 * generate_do_template_part()) operate on that global.
 *
 * Deliberately NOT using WordPress's official "Posts page" Reading
 * setting — that's only assignable once "homepage displays" is also
 * switched to a static page, which hasn't happened yet.
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
