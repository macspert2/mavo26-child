<?php
/**
 * Start Here — "Je prépare un grand projet" (plan-mid.md §5.2 group 4):
 * Tour du Monde / expatriation Angleterre. Neither maps to a
 * travel-finder filter_slug — they're real content pillars (referenced
 * in the homepage trust-bar copy) but not part of the recommendation
 * table, so this group is plain manual links, not catalog-backed like
 * the others. "Long voyage" was dropped — no real content for it yet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$projects = [
	[
		'title'       => __( 'Tour du Monde', 'mavo' ),
		'description' => __( 'Notre tour du monde en famille, étape par étape.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/category/voyages-avec-enfants/notre-tour-du-monde-2016/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2016/09/P4151267.jpg',
	],
	[
		'title'       => __( 'Expatriation en Angleterre', 'mavo' ),
		'description' => __( 'Vivre en Angleterre avec des enfants.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/notre-vie-en-angleterre/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2018/07/P6020767.jpg.webp',
	],
];

$items = [];
foreach ( $projects as $project ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $project['url'],
		'title'       => $project['title'],
		'description' => $project['description'],
		'image'       => $project['image'],
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
}
?>
<section id="mv-start-here-big-projects" class="mv-section mv-start-here-big-projects">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Vous préparez un grand projet ?', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 2,
			'items'   => $items,
		] );
		?>
	</div>
</section>
