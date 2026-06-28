<?php
/**
 * The template for displaying posts within the loop.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_search() ) :
	// Search results: Pattern B horizontal result tile (image left, body right).
	$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
	$tile_class = 'mv-tile mv-tile--result' . ( $thumb_url ? '' : ' mv-tile--no-media' );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $tile_class ); ?>>
		<?php if ( $thumb_url ) : ?>
			<a class="mv-tile__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<div class="mv-tile__media">
					<img class="mv-tile__img" src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy" decoding="async">
				</div>
			</a>
		<?php endif; ?>
		<div class="mv-tile__body">
			<h2 class="mv-tile__title">
				<a class="mv-tile__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
			<p class="mv-tile__description"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		</div>
	</article>

<?php else : ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php generate_do_microdata( 'article' ); ?>>
	<div class="inside-article">
		<?php
		/**
		 * generate_before_content hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_featured_page_header_inside_single - 10
		 */
		do_action( 'generate_before_content' );

		if ( generate_show_entry_header() ) :
			?>
			<header <?php generate_do_attr( 'entry-header' ); ?>>
				<?php
				/**
				 * generate_before_entry_title hook.
				 *
				 * @since 0.1
				 */
				do_action( 'generate_before_entry_title' );
		                do_action( 'generate_after_entry_header' ); // featured image before title

				if ( generate_show_title() ) {
					$params = generate_get_the_title_parameters();
					the_title( $params['before'], $params['after'] );
				}

				/**
				 * generate_after_entry_title hook.
				 *
				 * @since 0.1
				 *
				 * @hooked generate_post_meta - 10
				 */
/*				do_action( 'generate_after_entry_title' ); */
//				echo mavo22_meta_block_render();
// no date or comment link except on single content...
				?>
			</header>
			<?php
		endif;

		/**
		 * generate_after_entry_header hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_post_image - 10
		 */
//		do_action( 'generate_after_entry_header' );

		$itemprop = '';

		if ( 'microdata' === generate_get_schema_type() ) {
			$itemprop = ' itemprop="text"';
		}

		if ( generate_show_excerpt() ) :
			?>

			<div class="entry-summary"<?php echo $itemprop; // phpcs:ignore -- No escaping needed. ?>>
				<?php the_excerpt(); ?>
			</div>

		<?php else : ?>

			<div class="entry-content"<?php echo $itemprop; // phpcs:ignore -- No escaping needed. ?>>
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . __( 'Pages:', 'generatepress' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php
		endif;

		/**
		 * generate_after_entry_content hook.
		 *
		 * @since 0.1
		 *
		 * @hooked generate_footer_meta - 10
		 */
//		do_action( 'generate_after_entry_content' );

		/**
		 * generate_after_content hook.
		 *
		 * @since 0.1
		 */
		do_action( 'generate_after_content' );
if (($wp_query->current_post + 1) < ($wp_query->post_count)) {
   echo '<div class="post-item-divider"></div>';
}
		?>
	</div>
</article>

<?php endif; ?>
