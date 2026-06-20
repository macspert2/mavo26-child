<?php
/**
 * Shared grid wrapper. Renders a list of pre-rendered card items inside
 * a `.mv-grid` container so card markup stays in mv-shared/card-*.php.
 *
 * Usage:
 *   get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
 *       'columns' => 4,        // 2-4, matches .mv-grid--{n} in mv-home.css
 *       'items'   => [ $html1, $html2, ... ], // already-rendered card HTML
 *   ] );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$columns = (int) ( $args['columns'] ?? 3 );
$columns = max( 2, min( 4, $columns ) );
$items   = $args['items'] ?? [];

if ( empty( $items ) ) {
	return;
}
?>
<div class="mv-grid mv-grid--<?php echo esc_attr( $columns ); ?>">
	<?php foreach ( $items as $item_html ) : ?>
		<?php echo $item_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-rendered/escaped by the card partial. ?>
	<?php endforeach; ?>
</div>
