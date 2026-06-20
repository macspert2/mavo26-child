<?php
/**
 * Homepage recent posts — Section 7 (plan-mid.md §4.1).
 *
 * 3 latest posts in the current language. Kept below the evergreen
 * pathway sections on purpose (plan-mid.md §4.1 Section 7: "place lower
 * than evergreen routes"). The "view all" link is "#" for every language
 * — no /blog/ (or equivalent) latest-posts page yet (plan Phase 9).
 *
 * Shared across FR/EN/DE homepages (query logic doesn't differ by
 * language), with just the label text switched — unlike hero/about-mini/
 * destinations, which differ enough in content to warrant separate files
 * per language (see template-parts/mv-home-en/, mv-home-de/).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$labels = [
	'fr' => [ 'title' => 'Nos dernières aventures', 'more' => 'Voir tous les articles' ],
	'en' => [ 'title' => 'Our latest adventures', 'more' => 'See all articles' ],
	'de' => [ 'title' => 'Unsere neuesten Abenteuer', 'more' => 'Alle Artikel ansehen' ],
];
$label = $labels[ $lang ] ?? $labels['fr'];

$query_args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
];

if ( function_exists( 'pll_current_language' ) ) {
	$query_args['lang'] = $lang;
}

$recent_query = new WP_Query( $query_args );

if ( ! $recent_query->have_posts() ) {
	return;
}

$items = [];
foreach ( $recent_query->posts as $recent_post ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-post', null, [
		'post' => $recent_post,
	] );
	$items[] = ob_get_clean();
}
?>
<section class="mv-section mv-recent-posts">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => $label['title'],
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 3,
			'items'   => $items,
		] );
		?>
		<p class="mv-recent-posts__more">
			<a class="mv-button mv-button--secondary" href="#">
				<?php echo esc_html( $label['more'] ); ?>
			</a>
		</p>
	</div>
</section>
