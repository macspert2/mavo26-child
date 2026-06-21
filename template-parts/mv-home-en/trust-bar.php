<?php
/**
 * English homepage trust bar. "1200+ articles" from the FR version is
 * dropped here — that figure is the French archive's size, and would be
 * misleading next to <100 English posts. The other stats describe the
 * family's real travel history, which is true regardless of language.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = [
	[ 'number' => __( 'Since 2009', 'mavo' ), 'label' => __( '15+ years of family travel', 'mavo' ) ],
	[ 'number' => __( '180+', 'mavo' ), 'label' => __( 'family trips', 'mavo' ) ],
	[ 'number' => __( 'Round-the-world', 'mavo' ), 'label' => __( 'family trip', 'mavo' ) ],
	[ 'number' => __( 'Living', 'mavo' ), 'label' => __( 'in England', 'mavo' ) ],
];
?>
<section class="mv-section mv-trust-bar">
	<div class="mv-container">
		<ul class="mv-trust-bar__list">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="mv-trust-stat">
					<span class="mv-trust-stat__number"><?php echo esc_html( $stat['number'] ); ?></span>
					<span class="mv-trust-stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
