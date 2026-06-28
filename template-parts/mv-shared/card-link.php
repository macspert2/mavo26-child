<?php
/**
 * Shared text/directory link tile — Pattern A (whole tile is one link).
 *
 * Use for destination, trip-type, season, age, duration, and budget
 * navigation tiles. Images are intentionally omitted; these are editorial
 * text tiles, not article recommendation cards.
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/card-link', null, [
 *       'url'         => '#',
 *       'title'       => 'France en famille',
 *       'description' => 'Itinéraires et conseils pour voyager en France avec des enfants.',
 *       'badge'       => '',     // optional small label
 *       'variant'     => '',     // 'compact' adds mv-tile--compact; others are ignored
 *   ] );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url         = $args['url'] ?? '#';
$title       = $args['title'] ?? '';
$description = $args['description'] ?? '';
$badge       = $args['badge'] ?? '';
$variant     = $args['variant'] ?? '';

if ( '' === $title ) {
	return;
}

$classes = 'mv-tile mv-tile--text';
if ( 'compact' === $variant ) {
	$classes .= ' mv-tile--compact';
}
?>
<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $url ); ?>">
	<?php if ( $badge ) : ?>
		<span class="mv-tile__badge"><?php echo esc_html( $badge ); ?></span>
	<?php endif; ?>
	<span class="mv-tile__title"><?php echo esc_html( $title ); ?></span>
	<?php if ( $description ) : ?>
		<span class="mv-tile__description"><?php echo esc_html( $description ); ?></span>
	<?php endif; ?>
</a>
