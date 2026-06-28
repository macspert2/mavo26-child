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
<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
	<?php if ( $image_url ) : ?>
		<span class="mv-tile__media">
			<img class="mv-tile__img" src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" decoding="async">
		</span>
	<?php endif; ?>
	<span class="mv-tile__body">
		<span class="mv-tile__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
		<?php if ( $show_excerpt ) : ?>
			<span class="mv-tile__description"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $post ) ) ); ?></span>
		<?php endif; ?>
	</span>
</a>
