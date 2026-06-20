<?php
/**
 * Shared link card (used for pathway / destination style cards).
 *
 * Single outer link only — no nested links inside the card markup.
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/card-link', null, [
 *       'url'         => '#',
 *       'title'       => 'France en famille',
 *       'description' => 'Itinéraires et conseils pour voyager en France avec des enfants.',
 *       'image'       => '', // optional image URL
 *       'badge'       => '', // optional small label, e.g. "Nouveau"
 *       'variant'     => '', // optional extra class, e.g. "pathway"
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

$classes = 'mv-card mv-card--link';
if ( $variant ) {
	$classes .= ' mv-card--' . sanitize_html_class( $variant );
}
?>
<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $url ); ?>">
	<?php if ( $image ) : ?>
		<span class="mv-card__image">
			<img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy">
		</span>
	<?php endif; ?>
	<span class="mv-card__body">
		<?php if ( $badge ) : ?>
			<span class="mv-card__badge"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<span class="mv-card__title"><?php echo esc_html( $title ); ?></span>
		<?php if ( $description ) : ?>
			<span class="mv-card__description"><?php echo esc_html( $description ); ?></span>
		<?php endif; ?>
	</span>
</a>
