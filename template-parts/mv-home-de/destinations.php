<?php
/**
 * German homepage destination tiles — hardcoded, same rationale as
 * template-parts/mv-home-en/destinations.php. Links go to real tag
 * archives, verified live: Spanien (11 posts), Vereinigtes Königreich
 * (11), Italien (10), Griechenland (8) — the top 4 by post count. Each
 * now has its own real photo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$destinations = [
	[
		'title'       => __( 'Spanien', 'mavo' ),
		'description' => __( 'Familienreisen durch Spanien.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/tag/spanien/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2019/10/P9031799.jpeg.webp',
	],
	[
		'title'       => __( 'Vereinigtes Königreich', 'mavo' ),
		'description' => __( 'Familienleben und Reisen im Vereinigten Königreich.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/tag/vereinigtes-konigreich-de/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2026/03/IMG_9890.jpeg.webp',
	],
	[
		'title'       => __( 'Italien', 'mavo' ),
		'description' => __( 'Familienreisen durch Italien.', 'mavo' ),
		'url'         => 'https://www.mamanvoyage.com/de/tag/italien/',
		'image'       => 'https://www.mamanvoyage.com/wp-content/uploads/2016/11/DSCF0361.jpg.webp',
	],
	[
		'title'       => __( 'Griechenland', 'mavo' ),
		'description' => __( 'Familienreisen durch Griechenland.', 'mavo' ),
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
