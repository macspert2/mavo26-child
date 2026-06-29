<?php
/**
 * The template for displaying Archive pages.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

	<div <?php generate_do_attr( 'content' ); ?>>
		<main <?php generate_do_attr( 'main' ); ?>><div class="entry-content">
			<?php
			/**
			 * generate_before_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_before_main_content' );

			if ( generate_has_default_loop() ) {
				if ( have_posts() ) :

					/**
					 * generate_archive_title hook.
					 *
					 * @since 0.1
					 *
					 * @hooked generate_archive_title - 10
					 */
					echo geo_tagger_term_breadcrumb();
					do_action( 'generate_archive_title' );

					/**
					 * generate_before_loop hook.
					 *
					 * @since 3.1.0
					 */
					do_action( 'generate_before_loop', 'archive' );

					$current_geo = null;
					$queried_obj = get_queried_object();
					if ( $queried_obj instanceof \WP_Term && function_exists( 'mv_current_geo_from_term' ) ) {
						$current_geo = mv_current_geo_from_term( $queried_obj );
					}

					$output = '<div class="mv-tile-grid mv-archive-grid mv-archive-grid--wide">';

					while ( have_posts() ) :
						the_post();
						$thumb_url  = get_the_post_thumbnail_url( null, 'large' );
						$tile_class = 'mv-tile mv-tile--overlay' . ( $thumb_url ? '' : ' mv-tile--no-media' );
						$excerpt    = wp_trim_words( get_the_excerpt(), 22 );

						$output .= '<a class="' . esc_attr( $tile_class ) . '" href="' . esc_url( get_permalink() ) . '">';
						if ( $thumb_url ) {
							$output .= '<span class="mv-tile__media">'
								. '<img class="mv-tile__img" src="' . esc_url( $thumb_url ) . '" alt="" loading="lazy" decoding="async">'
								. '</span>';
						}
						$output .= '<span class="mv-tile__body">';
						if ( function_exists( 'mv_tile_badges' ) ) {
							$badge_args = [ 'context' => 'geo_hub', 'limit' => 2 ];
							if ( $current_geo ) {
								$badge_args['current_geo'] = $current_geo;
							}
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$output .= mv_tile_badges( get_the_ID(), $badge_args );
						}
						$output .= '<span class="mv-tile__title">' . esc_html( get_the_title() ) . '</span>'
							. ( $excerpt ? '<span class="mv-tile__description">' . esc_html( $excerpt ) . '</span>' : '' )
							. '</span>';
						$output .= '</a>';
					endwhile;

					$output .= '</div><div style="clear:both;"></div>';
					echo $output;

					/**
					 * generate_after_loop hook.
					 *
					 * @since 2.3
					 */
					do_action( 'generate_after_loop', 'archive' );

				else :

					generate_do_template_part( 'none' );

				endif;
			}

			/**
			 * generate_after_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_after_main_content' );
			?>
		</main></div>
	</div>

	<?php
	/**
	 * generate_after_primary_content_area hook.
	 *
	 * @since 2.0
	 */
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
