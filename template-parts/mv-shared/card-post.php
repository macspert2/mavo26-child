<?php
/**
 * Shared post card — featured image, title, excerpt, single outer link.
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
$image        = get_the_post_thumbnail_url( $post, 'medium_large' );
?>
<a class="mv-card mv-card--post" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
	<?php if ( $image ) : ?>
		<span class="mv-card__image">
			<img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy">
		</span>
	<?php endif; ?>
	<span class="mv-card__body">
		<span class="mv-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
		<?php if ( $show_excerpt ) : ?>
			<span class="mv-card__description"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $post ) ) ); ?></span>
		<?php endif; ?>
	</span>
</a>
