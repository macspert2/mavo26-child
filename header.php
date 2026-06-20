<?php
/**
 * The template for displaying the header.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php generate_do_microdata( 'body' ); ?>>
	<?php
	/**
	 * wp_body_open hook.
	 *
	 * @since 2.3
	 */
	do_action( 'wp_body_open' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core WP hook.

	/**
	 * generate_before_header hook.
	 *
	 * @since 0.1
	 *
	 * @hooked generate_do_skip_to_content_link - 2
	 * @hooked generate_top_bar - 5
	 * @hooked generate_add_navigation_before_header - 5
	 */
	do_action( 'generate_before_header' );

	/**
	 * generate_header hook.
	 *
	 * @since 1.3.42
	 *
	 * @hooked generate_construct_header - 10
	 */
	do_action( 'generate_header' );

/*	if (pll_current_language()=="fr") {
		echo '<div class="mavo-sticky"><div class="mavo-logo">'.do_shortcode('[mavo_header_slider]').'</div><div class="mavo-nav">';
		ubermenu( 'twentytwo' , array( 'menu' => 1329 ) );
        } elseif (pll_current_language()=="en") {
                echo '<div class="mavo-sticky"><div class="mavo-logo">'.do_shortcode('[mavo_header_slider]').'</div><div class="mavo-nav">';
                ubermenu( 'twentytwo' , array( 'menu' => 7976 ) );
	} else {
                echo '<div class="mavo-sticky"><div class="mavo-logo">'.do_shortcode('[mavo_header_slider').'</div><div class="mavo-nav">';
		ubermenu( 'twentytwo' , array( 'menu' => 12924 ) );
	}
	echo '</div></div>'.do_shortcode('[mavo_hero_slider]'); */
	echo '<div class="mavo-sticky"><div class="mavo-logo">'.do_shortcode('[mavo_header_slider]').'</div>';
	echo '<div class="mavo-nav">'.do_shortcode('[mavo_menu]');
	echo '</div></div>'.do_shortcode('[mavo_hero_slider]');



	/**
	 * generate_after_header hook.
	 *
	 * @since 0.1
	 *
	 * @hooked generate_featured_page_header - 10
	 */
	do_action( 'generate_after_header' );
	?>

	<div <?php generate_do_attr( 'page' ); ?>>
		<?php
		/**
		 * generate_inside_site_container hook.
		 *
		 * @since 2.4
		 */
		do_action( 'generate_inside_site_container' );
		?>
		<div <?php generate_do_attr( 'site-content' ); ?>>
			<?php
			/**
			 * generate_inside_container hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_inside_container' );
