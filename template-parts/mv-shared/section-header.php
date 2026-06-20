<?php
/**
 * Shared section header.
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/section-header', null, [
 *       'title'    => 'Section title',
 *       'subtitle' => 'Optional supporting line.',
 *       'heading'  => 'h2', // optional, defaults to h2
 *   ] );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = $args['title'] ?? '';
$subtitle = $args['subtitle'] ?? '';
$heading  = $args['heading'] ?? 'h2';

if ( '' === $title ) {
	return;
}
?>
<header class="mv-section__header">
	<?php echo sprintf( '<%1$s class="mv-section__title">%2$s</%1$s>', tag_escape( $heading ), esc_html( $title ) ); ?>
	<?php if ( $subtitle ) : ?>
		<p class="mv-section__subtitle"><?php echo esc_html( $subtitle ); ?></p>
	<?php endif; ?>
</header>
