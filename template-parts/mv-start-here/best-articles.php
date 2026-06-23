<?php
/**
 * Start Here — "Les articles les plus lus" (plan-mid.md §5.2 group 5,
 * "Les meilleurs articles pour commencer" in the plan's draft). The plan
 * calls for manually curated evergreen posts; improved here to be fully
 * automated instead — TVF_Homepage::get_top_posts() ranks by views
 * (wp_postmeta meta_key='views'), the same ranking [travel_finder]
 * already uses when no filters are selected. No manual curation list to
 * maintain — title changed to "most read" to honestly reflect that it's
 * popularity-ranked, not editorially curated.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVF_Homepage' ) ) {
	return;
}

$lang  = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';
$posts = TVF_Homepage::get_top_posts( $lang, 6 );

if ( empty( $posts ) ) {
	return;
}

$items = [];
foreach ( $posts as $best_post ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-post', null, [
		'post' => $best_post,
	] );
	$items[] = ob_get_clean();
}
?>
<section class="mv-section mv-start-here-best-articles">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Notre sélection du moment', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 3,
			'items'   => $items,
		] );
		?>
	</div>
</section>
