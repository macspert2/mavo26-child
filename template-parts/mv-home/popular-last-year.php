<?php
/**
 * Homepage "popular last year, same month" — added directly on request,
 * not part of plan-mid.md. Sits below family-travel-themes.
 *
 * Reads wp_rpp_monthly_snapshots (a pre-existing stats table from a
 * separate plugin, unrelated to wp_tvf_post_filter) via
 * TVF_Popular_Snapshots, added in the mavo-travel-finder plugin since
 * that's where homepage-tile data resolution already lives.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVF_Popular_Snapshots' ) ) {
	return;
}

$month = TVF_Popular_Snapshots::same_month_last_year();
$posts = TVF_Popular_Snapshots::get_top_posts_for_month( $month, 6 );

if ( empty( $posts ) ) {
	return;
}

$items = [];
foreach ( $posts as $popular_post ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-post', null, [
		'post' => $popular_post,
	] );
	$items[] = ob_get_clean();
}
?>
<section class="mv-section mv-popular-last-year">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Populaire à la même période l’an dernier', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 3,
			'items'   => $items,
		] );
		?>
	</div>
</section>
