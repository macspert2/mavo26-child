<?php
/**
 * German homepage trust bar — same rationale as
 * template-parts/mv-home-en/trust-bar.php (FR's "1200+ articles" dropped
 * as misleading for the much smaller German archive). Draft translation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = [
	[ 'number' => __( 'Seit 2009', 'mavo' ), 'label' => __( '15+ Jahre Familienreisen', 'mavo' ) ],
	[ 'number' => __( '180+', 'mavo' ), 'label' => __( 'Reisen mit Kindern', 'mavo' ) ],
	[ 'number' => __( 'Weltreise', 'mavo' ), 'label' => __( 'als Familie', 'mavo' ) ],
	[ 'number' => __( 'Leben als Expat', 'mavo' ), 'label' => __( 'in England', 'mavo' ) ],
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
