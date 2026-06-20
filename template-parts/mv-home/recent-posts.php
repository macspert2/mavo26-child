<?php
/**
 * Homepage recent posts — Section 7 (plan-mid.md §4.1).
 *
 * 3 latest posts in the current language. Kept below the evergreen
 * pathway sections on purpose (plan-mid.md §4.1 Section 7: "place lower
 * than evergreen routes"). No "view all" link yet — there's no /blog/
 * (or equivalent) latest-posts page until plan-mid.md Phase 9.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query_args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
];

if ( function_exists( 'pll_current_language' ) ) {
	$query_args['lang'] = pll_current_language( 'slug' );
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
			'title' => __( 'Derniers articles', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 3,
			'items'   => $items,
		] );
		?>
	</div>
</section>
