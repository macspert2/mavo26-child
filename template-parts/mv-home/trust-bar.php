<?php
/**
 * Homepage trust bar — Section 2 (plan-mid.md §4.1).
 *
 * Numbers are placeholders; keep them configurable (e.g. theme customizer
 * or plugin option) once this leaves the prototype stage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = [
	[ 'number' => __( 'Depuis 2009', 'mavo' ), 'label' => __( '15+ ans de voyages en famille', 'mavo' ) ],
	[ 'number' => __( '180+', 'mavo' ), 'label' => __( 'voyages avec enfants', 'mavo' ) ],
	[ 'number' => __( '1200+', 'mavo' ), 'label' => __( 'articles publiés', 'mavo' ) ],
	[ 'number' => __( 'Tour du monde', 'mavo' ), 'label' => __( 'en famille', 'mavo' ) ],
	[ 'number' => __( 'Expatriation', 'mavo' ), 'label' => __( 'en Angleterre', 'mavo' ) ],
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
