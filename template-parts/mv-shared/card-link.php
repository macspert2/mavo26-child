<?php
/**
 * Shared link tile — Pattern A (whole tile is one link, no nested anchors).
 *
 * Adaptive layout:
 * - image provided → mv-tile--media (image above, text below)
 * - no image       → mv-tile--text  (accent bar + text only)
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/card-link', null, [
 *       'url'         => '#',
 *       'title'       => 'France en famille',
 *       'description' => 'Itinéraires et conseils pour voyager en France avec des enfants.',
 *       'image'       => '', // optional image URL
 *       'badge'       => '', // optional small label
 *       'variant'     => '', // 'compact' adds mv-tile--compact; others ignored
 *   ] );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url         = $args['url'] ?? '#';
$title       = $args['title'] ?? '';
$description = $args['description'] ?? '';
$image       = $args['image'] ?? '';
$badge       = $args['badge'] ?? '';
$variant     = $args['variant'] ?? '';

if ( '' === $title ) {
	return;
}

$has_image = ! empty( $image );
$classes   = 'mv-tile ' . ( $has_image ? 'mv-tile--media' : 'mv-tile--text' );
if ( 'compact' === $variant ) {
	$classes .= ' mv-tile--compact';
}
?>
<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $url ); ?>">
	<?php if ( $has_image ) : ?>
		<span class="mv-tile__media">
			<img class="mv-tile__img" src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" decoding="async">
		</span>
	<?php endif; ?>
	<span class="mv-tile__body">
		<?php if ( $badge ) : ?>
			<span class="mv-tile__badge"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<span class="mv-tile__title"><?php echo esc_html( $title ); ?></span>
		<?php if ( $description ) : ?>
			<span class="mv-tile__description"><?php echo esc_html( $description ); ?></span>
		<?php endif; ?>
	</span>
</a>
