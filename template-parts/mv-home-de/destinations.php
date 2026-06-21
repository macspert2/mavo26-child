<?php
/**
 * German homepage destination tiles — hardcoded, same rationale as
 * template-parts/mv-home-en/destinations.php. Links go to real tag
 * archives, verified live: Spanien (11 posts), England/Großbritannien
 * (11), Italien (10), Griechenland (8) — the top 4 by post count. Each
 * has its own real photo. "Vereinigtes Königreich" renamed to "England
 * & Großbritannien" per plan2.md §15.2 — too formal/bureaucratic-
 * sounding for the blog's casual tone.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$destinations = [
	[
		'title'       => __( 'Spanien', 'mavo' ),
		'description' => __( 'Sonne, Inseln, Städte und entspannte Ideen für Familienreisen.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/tag/spanien/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2019/10/P9031799.jpeg.webp',
	],
	[
		'title'       => __( 'England & Großbritannien', 'mavo' ),
		'description' => __( 'Ausflüge, Zugreisen, London und Südengland — entdeckt aus unserem Leben in England.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/tag/vereinigtes-konigreich-de/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2026/03/IMG_9890.jpeg.webp',
	],
	[
		'title'       => __( 'Italien', 'mavo' ),
		'description' => __( 'Städtereisen, Inseln, Wanderungen und Roadtrips mit Kindern.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/tag/italien/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2016/11/DSCF0361.jpg.webp',
	],
	[
		'title'       => __( 'Griechenland', 'mavo' ),
		'description' => __( 'Inseln, Dörfer, Badepausen und einfache Routen mit Kindern.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/tag/griechenland/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2022/12/5EM22156.jpg.webp',
	],
];

$items = [];
foreach ( $destinations as $destination ) {
	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => $destination['url'],
		'title'       => $destination['title'],
		'description' => $destination['description'],
		'image'       => $destination['image'],
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
}
?>
<section class="mv-section mv-featured-destinations" id="mv-destinations">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => __( 'Beliebte Reiseziele', 'mavo' ),
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => 4,
			'items'   => $items,
		] );
		?>
	</div>
</section>
