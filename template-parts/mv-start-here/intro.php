<?php
/**
 * Start Here intro — page H1 + short framing line + "how to use this
 * page" guidance + anchor jump-links, per plan2.md §13.3/§13.6.
 *
 * page-commencez-ici.php is a fully custom template (get_header() +
 * template parts, no content-page.php in the loop), so the WordPress
 * page title never renders on its own — same situation as
 * page-accueil-prototype.php, where hero.php's <h1> is the only one.
 * This is that page's missing H1, using the real page title so it stays
 * correct if the page is ever renamed.
 *
 * Anchor targets are the IDs added to each group's own <section> tag
 * (catalog-tile-grid.php and big-projects.php), matching their
 * section_class values rather than separate hardcoded IDs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jump_links = [
	'mv-start-here-destination'  => __( 'Destinations', 'mavo' ),
	'mv-start-here-with-whom'    => __( 'Âge des enfants', 'mavo' ),
	'mv-start-here-trip-type'    => __( 'Type de voyage', 'mavo' ),
	'mv-start-here-duration'     => __( 'Durée', 'mavo' ),
	'mv-start-here-budget'       => __( 'Budget', 'mavo' ),
	'mv-start-here-big-projects' => __( 'Grand projet', 'mavo' ),
];
?>
<section class="mv-section mv-start-here-intro">
	<div class="mv-container">
		<h1 class="mv-start-here-intro__title"><?php echo esc_html( get_the_title() ); ?></h1>
		<p>
			<?php esc_html_e( 'Choisissez une destination, l’âge de vos enfants ou le type de séjour qui vous correspond : voici nos meilleures idées selon votre situation.', 'mavo' ); ?>
		</p>
		<ul class="mv-start-here-intro__guidance">
			<li><?php esc_html_e( 'Vous avez déjà une destination en tête ? Commencez par les pays.', 'mavo' ); ?></li>
			<li><?php esc_html_e( 'Vous cherchez une idée adaptée à l’âge de vos enfants ? Utilisez les rubriques bébé, jeunes enfants ou ados.', 'mavo' ); ?></li>
			<li><?php esc_html_e( 'Vous hésitez encore ? Les sélections par durée, budget et type de séjour sont faites pour ça.', 'mavo' ); ?></li>
		</ul>
		<nav class="mv-start-here-intro__jumplinks" aria-label="<?php esc_attr_e( 'Aller directement à une rubrique', 'mavo' ); ?>">
			<span class="mv-start-here-intro__jumplinks-label"><?php esc_html_e( 'Aller directement à :', 'mavo' ); ?></span>
			<?php foreach ( $jump_links as $anchor_id => $label ) : ?>
				<a href="#<?php echo esc_attr( $anchor_id ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>
