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

					$output = '<ul class="wp-block-post-template archive-page">';

					while ( have_posts() ) :

						the_post();
						$thumbnail = has_post_thumbnail()
							? '<a href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( null, 'full' ) . '</a>'
							: '';
						$output .= '<li class="wp-block-post">
<figure class="alignwide wp-block-post-featured-image">' . $thumbnail . '</figure>
<a href="' . get_permalink() . '" target="_self" class="wp-card">
<h2 class="alignwide wp-block-post-title has-x-large-font-size has-system-font-font-family" style="font-style: normal;font-weight: 200">' . get_the_title() . '</h2>
<div class="wp-block-post-excerpt">
<p>' . get_the_excerpt() . '</p>
</div></a>
</li>'; 

//						generate_do_template_part( 'archive' );

					endwhile;

					$output .= '</ul><div style="clear:both;"></div>';
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
