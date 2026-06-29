<?php
/**
 * Shared post media tile — Pattern A (whole tile is one link).
 *
 * Use for article recommendation cards: featured image above, title and
 * excerpt below. Hides the media area if no thumbnail is available.
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/card-post', null, [
 *       'post'         => $post, // WP_Post or post ID
 *       'show_excerpt' => true,  // optional, defaults to true
 *   ] );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post = $args['post'] ?? null;
$post = get_post( $post );

if ( ! $post ) {
	return;
}

$show_excerpt = $args['show_excerpt'] ?? true;
$image_url    = get_the_post_thumbnail_url( $post, 'medium_large' );
$classes      = 'mv-tile mv-tile--media' . ( $image_url ? '' : ' mv-tile--no-media' );
?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<?php if ( $image_url ) : ?>
		<span class="mv-tile__media">
			<img class="mv-tile__img" src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" decoding="async">
		</span>
	<?php endif; ?>
	<span class="mv-tile__body">
		<?php if ( function_exists( 'mv_tile_badges' ) ) : ?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo mv_tile_badges( $post->ID, [ 'context' => 'homepage_media', 'limit' => 2, 'link_badges' => true ] ); ?>
		<?php endif; ?>
		<span class="mv-tile__title">
			<a class="mv-tile__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
		</span>
		<?php if ( $show_excerpt ) : ?>
			<span class="mv-tile__description"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $post ) ) ); ?></span>
		<?php endif; ?>
	</span>
</div>
