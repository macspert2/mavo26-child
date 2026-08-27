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
 * matched directly), EN /en/blog-en/ and DE /de/blog-de/ (slugs
 * `blog-en`/`blog-de` — Polylang wouldn't allow reusing the identical
 * slug `blog` across languages here, unlike other pages in this
 * project, so the URL itself carries the language suffix too, not just
 * the slug internally — confirmed live; page-blog-en.php/page-blog-de.php
 * just require this file directly). The query doesn't differ by language.
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
 * That same "it's really a page, not an archive" trick is what makes the
 * Yoast filters below necessary: Yoast canonicalises /blog/page/N/ back
 * to /blog/, so every page past the first has to be pointed at itself.
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

$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

// Pretty-permalink pagination on a *page* request (e.g. /blog/page/2/)
// never sets $_GET['paged'] — WordPress's rewrite rules parse that URL
// segment into the 'page' query var instead ('paged' is reserved for
// true archive/home requests). Checking both covers whichever one
// WordPress actually populates here, rather than betting on just one.
//
// Resolved before get_header() because the SEO filters below need it:
// wp_head() runs inside get_header(), so Yoast has already printed the
// canonical by the time the $wp_query swap further down happens.
$paged = (int) get_query_var( 'paged' );
if ( ! $paged ) {
	$paged = (int) get_query_var( 'page' );
}
$paged = max( 1, $paged );

$query_args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => get_option( 'posts_per_page' ),
	'paged'               => $paged,
	'ignore_sticky_posts' => true,
];

if ( function_exists( 'pll_current_language' ) ) {
	$query_args['lang'] = $lang;
}

// Yoast sees a singular page here, not an archive (the "Posts page"
// Reading setting is deliberately unset — see above), so it canonicalises
// every /blog/page/N/ back to /blog/, declaring ~105 pages of distinct
// posts to be duplicates of page 1. Point each page at itself instead,
// which is Google's guidance for a paginated series. Registered before
// get_header() so the filters are in place when wp_head() runs — same
// reason as the body_class filter above.
//
// Covers FR/EN/DE at once: page-blog-en.php and page-blog-de.php require
// this file, so the permalink Yoast hands the filter is already the
// correct per-language one.
if ( $paged > 1 ) {
	// A page number past the end (/blog/page/9999/) still returns 200 with
	// an empty loop rather than a 404. Self-canonicalising those would turn
	// an unbounded range of near-empty pages into indexable duplicates, so
	// probe first and noindex instead. Cheapest possible probe: one ID, no
	// COUNT — this only runs on page 2+, never on the common page-1 request.
	$mv_probe = new WP_Query( array_merge( $query_args, [
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	] ) );

	if ( ! $mv_probe->have_posts() ) {
		// Yoast feeds core's wp_robots filter and core renders the tag, so
		// filtering last wins — same mechanism mavo-travel-finder uses for
		// its ?f= views.
		add_filter( 'wp_robots', static function ( array $robots ): array {
			unset( $robots['index'], $robots['nofollow'] );
			return array_merge( [ 'noindex' => true, 'follow' => true ], $robots );
		}, PHP_INT_MAX );
	} else {
		$mv_paged_url = static function ( $url ) use ( $paged ) {
			// Yoast intentionally emits an empty canonical on noindex pages —
			// leave that untouched so its policy still wins.
			if ( ! is_string( $url ) || '' === $url ) {
				return $url;
			}

			global $wp_rewrite;
			$base = ( $wp_rewrite && $wp_rewrite->pagination_base ) ? $wp_rewrite->pagination_base : 'page';

			return user_trailingslashit( trailingslashit( $url ) . $base . '/' . $paged, 'paged' );
		};

		// og:url has to move with the canonical, or the page contradicts itself.
		add_filter( 'wpseo_canonical',     $mv_paged_url );
		add_filter( 'wpseo_opengraph_url', $mv_paged_url );

		// Without this every page shares one title, which lands in Search
		// Console as duplicate titles once they stop canonicalising to page 1.
		$mv_page_label = [ 'fr' => 'Page', 'en' => 'Page', 'de' => 'Seite' ][ $lang ] ?? 'Page';
		add_filter( 'wpseo_title', static function ( $title ) use ( $paged, $mv_page_label ) {
			return ( is_string( $title ) && '' !== $title )
				? $title . ' - ' . $mv_page_label . ' ' . $paged
				: $title;
		} );
	}
}

get_header();

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
