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

/**
 * Titles/subtitles reframed per plan2.md §9 — the old fallback title
 * ("Actuellement les plus lus" / "Currently most read") and non-fallback
 * title ("Les articles les plus lus en %s l'an dernier") exposed the
 * analytics logic instead of giving the visitor a reason to read.
 * EN/DE's non-fallback case isn't addressed by the plan (it rarely
 * fires today — both languages' "last year" query is reliably empty,
 * see TVF_Popular_Snapshots), so it keeps its previous wording rather
 * than guessing at unrequested copy.
 */
if ( $is_fallback ) {
	$titles = [
		'fr' => 'Les idées de voyage qui plaisent en ce moment',
		'en' => 'Popular family travel ideas right now',
		'de' => 'Beliebte Reiseideen im Moment',
	];
	$subtitles = [
		'fr' => 'Une sélection de nos articles les plus appréciés actuellement.',
		'en' => 'A seasonal selection of articles readers often come back to at this time of year.',
		'de' => 'Eine saisonale Auswahl von Artikeln, die Familien zu dieser Jahreszeit besonders oft lesen.',
	];
	$title    = $titles[ $lang ] ?? $titles['fr'];
	$subtitle = $subtitles[ $lang ] ?? $subtitles['fr'];
} elseif ( 'fr' === $lang ) {
	$title    = 'Vos idées de voyage préférées pour cette période';
	$subtitle = 'Une sélection d’articles souvent consultés à cette période de l’année.';
} else {
	$month_label = date_i18n( 'F', strtotime( $month ) );
	/* translators: %s: localized month name, e.g. "June" / "Juni" */
	$title_formats = [
		'en' => 'Most read in %s last year',
		'de' => 'Meistgelesen im %s letzten Jahres',
	];
	$title    = sprintf( $title_formats[ $lang ] ?? $title_formats['en'], $month_label );
	$subtitle = '';
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
			'title'    => $title,
			'subtitle' => $subtitle,
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 3,
			'items'   => $items,
		] );
		?>
	</div>
</section>
