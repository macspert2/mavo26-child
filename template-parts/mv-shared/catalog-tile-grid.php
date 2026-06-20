<?php
/**
 * A full section of catalog-backed tiles (TVF_Homepage) — section-header
 * + grid of card-link tiles, one per catalog key. Owns its own
 * <section>/<container> wrapper so it can skip itself entirely (no empty
 * shell) when the plugin is inactive or none of the keys have matches.
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
 *       'section_class'  => 'mv-family-travel-themes',
 *       'title'          => 'Voyager selon votre famille',
 *       'keys'           => [ 'bebe', 'une_semaine', 'nature_rando' ],
 *       'columns'        => 4,
 *       'posts_per_key'  => 3,      // optional, how many posts to check per key
 *       'link_to'        => 'focus', // optional: 'focus' (default) or 'full'
 *   ] );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVF_Homepage' ) ) {
	return;
}

$section_class = $args['section_class'] ?? '';
$title         = $args['title'] ?? '';
$keys          = $args['keys'] ?? [];
$columns       = (int) ( $args['columns'] ?? 4 );
$posts_per_key = (int) ( $args['posts_per_key'] ?? 3 );
$link_to       = $args['link_to'] ?? 'focus';

if ( empty( $keys ) ) {
	return;
}

$placeholder_image = 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg';
$link_base          = 'full' === $link_to
	? 'https://www.mamanvoyage.com/ou-partir-trouvez-votre-prochain-voyage/'
	: 'https://www.mamanvoyage.com/nos-idees-de-voyage/';
$lang               = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

$items = [];
foreach ( $keys as $key ) {
	$meta = TVF_Homepage::get_card_meta( $key );
	if ( ! $meta ) {
		continue;
	}

	$posts = TVF_Homepage::get_card_posts( $key, $lang, $posts_per_key );
	if ( empty( $posts ) ) {
		continue; // no matching posts yet — skip rather than show an empty promise.
	}

	$image = get_the_post_thumbnail_url( $posts[0], 'medium_large' ) ?: $placeholder_image;

	ob_start();
	get_template_part( 'template-parts/mv-shared/card-link', null, [
		'url'         => add_query_arg( 'f', implode( ',', $meta['slugs'] ), $link_base ),
		'title'       => $meta['label'],
		'description' => $meta['description'],
		'image'       => $image,
		'variant'     => 'pathway',
	] );
	$items[] = ob_get_clean();
}

if ( empty( $items ) ) {
	return;
}
?>
<section class="mv-section <?php echo esc_attr( $section_class ); ?>">
	<div class="mv-container">
		<?php
		get_template_part( 'template-parts/mv-shared/section-header', null, [
			'title' => $title,
		] );
		get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
			'columns' => $columns,
			'items'   => $items,
		] );
		?>
	</div>
</section>
