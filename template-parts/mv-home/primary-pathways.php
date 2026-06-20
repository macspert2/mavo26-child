<?php
/**
 * Homepage primary pathways — Section 3 (plan-mid.md §4.1).
 *
 * Each card should link to a hub, tag/category page, or a Start Here
 * anchor. Only "France en famille" has a confirmed URL today (/france/);
 * the rest are placeholders ("#") until those hubs/pages exist — update
 * the `url` value below once each destination is built.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pathways = [
	[
		'title'       => __( 'France en famille', 'mavo' ),
		'description' => __( 'Nos meilleures idées pour voyager en France avec des enfants.', 'mavo' ),
		'url'         => home_url( '/france/' ),
	],
	[
		'title'       => __( 'Europe avec enfants', 'mavo' ),
		'description' => __( 'Destinations européennes testées en famille.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'Angleterre / expatriation', 'mavo' ),
		'description' => __( 'Vivre et voyager en Angleterre avec des enfants.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'Voyager avec bébé', 'mavo' ),
		'description' => __( 'Conseils et retours d’expérience pour partir avec un bébé.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'Randonnées en famille', 'mavo' ),
		'description' => __( 'Itinéraires de rando adaptés aux enfants.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'Tour du Monde', 'mavo' ),
		'description' => __( 'Notre tour du monde en famille, étape par étape.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'City-trips', 'mavo' ),
		'description' => __( 'Week-ends et city-trips en famille.', 'mavo' ),
		'url'         => '#',
	],
	[
		'title'       => __( 'Road trips / campervan', 'mavo' ),
		'description' => __( 'Voyager en van ou en road trip avec des enfants.', 'mavo' ),
		'url'         => '#',
	],
];

$items = [];
foreach ( $pathways as $pathway ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $pathway['url'],
		'title'       => $pathway['title'],
		'description' => $pathway['description'],
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
}
?>
<section class="mv-section mv-primary-pathways">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Par où commencer ?', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
