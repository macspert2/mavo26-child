<?php
/**
 * Homepage "popular last year, same month" — added directly on request,
 * not part of plan-mid.md. Sits below family-travel-themes on the FR
 * homepage; shared as-is on the EN/DE homepages too (query logic doesn't
 * differ by language, see template-parts/mv-home/recent-posts.php for
 * the same reasoning), only the title text switches.
 *
 * Falls back to "currently popular" (all-time wp_postmeta view count,
 * TVF_Popular_Snapshots::get_most_viewed()) when "last year, same
 * month" has no snapshot data — e.g. German view tracking started more
 * recently than a year ago, so that query is always empty for 'de'
 * today. The fallback used to also pull from the snapshot table (most
 * recent month instead of last year), but that table is too thin for
 * EN/DE after language filtering (as little as 1 EN / 0 DE post) —
 * all-time postmeta views across every published post works much
 * better at this content volume.
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

$lang  = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';
$month = TVF_Popular_Snapshots::same_month_last_year();
$posts = TVF_Popular_Snapshots::get_top_posts_for_month( $month, $lang, 6 );
$is_fallback = false;

if ( empty( $posts ) ) {
	$posts       = TVF_Popular_Snapshots::get_most_viewed( $lang, 6 );
	$is_fallback = true;
}

if ( empty( $posts ) ) {
	return;
}

if ( $is_fallback ) {
	$titles = [
		'fr' => 'Actuellement les plus lus',
		'en' => 'Currently most read',
		'de' => 'Aktuell meistgelesen',
	];
	$title = $titles[ $lang ] ?? $titles['fr'];
} else {
	$month_label = date_i18n( 'F', strtotime( $month ) );
	/* translators: %s: localized month name, e.g. "juin" / "June" / "Juni" */
	$title_formats = [
		'fr' => 'Les articles les plus lus en %s l’an dernier',
		'en' => 'Most read in %s last year',
		'de' => 'Meistgelesen im %s letzten Jahres',
	];
	$title = sprintf( $title_formats[ $lang ] ?? $title_formats['fr'], $month_label );
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
			'title' => $title,
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 3,
			'items'   => $items,
		] );
		?>
	</div>
</section>
