<?php
/**
 * Start Here — "Je prépare un grand projet" (plan-mid.md §5.2 group 4):
 * Tour du Monde / long voyage / expatriation Angleterre. None of these
 * map to a travel-finder filter_slug — they're real content pillars
 * (referenced in the homepage trust-bar copy) but not part of the
 * recommendation table, so this group is plain manual links, not
 * catalog-backed like the others.
 *
 * URLs are "#" placeholders — need the real hub/category/tag URLs for
 * Tour du Monde, long-format travel, and England expat content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$placeholder_image = 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg';

$projects = [
	[
		'title'       => __( 'Tour du Monde', 'mavo' ),
		'description' => __( 'Notre tour du monde en famille, étape par étape.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'Long voyage', 'mavo' ),
		'description' => __( 'Partir plusieurs semaines ou plusieurs mois en famille.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'Expatriation en Angleterre', 'mavo' ),
		'description' => __( 'Vivre en Angleterre avec des enfants.', 'mavo' ),
		'url'         => '#',
	],
];

$items = [];
foreach ( $projects as $project ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $project['url'],
		'title'       => $project['title'],
		'description' => $project['description'],
		'image'       => $placeholder_image,
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
}
?>
<section class="mv-section mv-start-here-big-projects">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Vous préparez un grand projet ?', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 3,
			'items'   => $items,
		] );
		?>
	</div>
</section>
