<?php
/**
 * Shortcodes for geo/country hub landing pages.
 *
 * [mv-geo-posts]   — featured/popular posts for a geo, card-post grid
 * [mv-geo-catalog] — catalog tile grid with geo pre-filter applied to links
 *
 * Example usage on /france/:
 *   [mv-geo-posts geo="france" title="À la une" limit="6" columns="3"]
 *   [mv-geo-catalog geo="france" title="Par type de séjour" keys="plage_cote,nature_rando,gastronomie,culture_histoire,velo,voile,activites_famille,detente,roadtrip" columns="4"]
 *   [mv-geo-catalog geo="france" title="Par âge" keys="bebe,jeunes_enfants,ados" columns="3"]
 *   [mv-geo-catalog geo="france" title="Par saison" keys="printemps,ete,automne,hiver" columns="4"]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [mv-geo-posts] — top or recent posts for a geo, rendered as a card-post grid.
 *
 * Attributes:
 *   geo        — filter slug / catalog key for the geo (e.g. "france"). Required.
 *   tag        — WP tag slug for "recent" queries (defaults to geo value).
 *   type       — "popular" (default, uses travel-finder rankings) or "recent" (by date).
 *   title      — section heading.
 *   limit      — number of posts (default 6).
 *   columns    — grid columns (default 3).
 *   background — "cream" / "blue-tint" / "" (default, white).
 *   more_url   — optional URL for a "see all" button below the grid.
 *   more_label — button label (default: "Voir tous les articles").
 */
function mv_shortcode_geo_posts( array $atts ): string {
	$atts = shortcode_atts(
		[
			'geo'        => '',
			'tag'        => '',
			'type'       => 'popular',
			'title'      => '',
			'limit'      => '6',
			'columns'    => '3',
			'background' => '',
			'more_url'   => '',
			'more_label' => 'Voir tous les articles',
		],
		$atts,
		'mv-geo-posts'
	);

	$geo        = sanitize_key( $atts['geo'] );
	$tag        = sanitize_key( $atts['tag'] ?: $geo );
	$type       = 'recent' === $atts['type'] ? 'recent' : 'popular';
	$limit      = max( 1, (int) $atts['limit'] );
	$columns    = max( 1, (int) $atts['columns'] );
	$title      = sanitize_text_field( $atts['title'] );
	$background = sanitize_html_class( $atts['background'] );
	$more_url   = esc_url_raw( $atts['more_url'] );
	$more_label = sanitize_text_field( $atts['more_label'] );
	$lang       = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

	$posts = [];

	if ( 'recent' === $type ) {
		$q_args = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'tag'                 => $tag,
		];
		if ( function_exists( 'pll_current_language' ) ) {
			$q_args['lang'] = $lang;
		}
		$q     = new WP_Query( $q_args );
		$posts = $q->posts;
		wp_reset_postdata();
	} elseif ( $geo && class_exists( 'TVF_Homepage' ) ) {
		$posts = TVF_Homepage::get_card_posts( $geo, $lang, $limit );
	}

	// Fallback: recent posts by tag when the travel-finder returns nothing.
	if ( empty( $posts ) && $tag ) {
		$q_args = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'tag'                 => $tag,
		];
		if ( function_exists( 'pll_current_language' ) ) {
			$q_args['lang'] = $lang;
		}
		$q     = new WP_Query( $q_args );
		$posts = $q->posts;
		wp_reset_postdata();
	}

	if ( empty( $posts ) ) {
		return '';
	}

	$badge_args = [ 'context' => 'geo_hub', 'limit' => 2 ];
	$current_geo = function_exists( 'mv_page_current_geo' ) ? mv_page_current_geo() : null;
	if ( $current_geo ) {
		$badge_args['current_geo'] = $current_geo;
	}

	$items = [];
	foreach ( $posts as $post ) {
		ob_start();
		get_template_part( 'template-parts/mv-shared/card-post', null, [
			'post'       => $post,
			'badge_args' => $badge_args,
		] );
		$items[] = ob_get_clean();
	}

	$section_classes = 'mv-section mv-geo-posts';
	if ( $background ) {
		$section_classes .= ' mv-section--bg-' . $background;
	}

	ob_start();
	?>
	<section class="<?php echo esc_attr( $section_classes ); ?>">
		<div class="mv-container">
			<?php
			get_template_part( 'template-parts/mv-shared/section-header', null, [ 'title' => $title ] );
			get_template_part( 'template-parts/mv-shared/grid-wrapper', null, [
				'columns' => $columns,
				'items'   => $items,
			] );
			?>
			<?php if ( $more_url ) : ?>
			<p class="mv-geo-posts__more">
				<a class="mv-button mv-button--secondary" href="<?php echo esc_url( $more_url ); ?>">
					<?php echo esc_html( $more_label ); ?>
				</a>
			</p>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'mv-geo-posts', 'mv_shortcode_geo_posts' );


/**
 * [mv-geo-catalog] — catalog tile grid with a geo filter pre-applied to links.
 *
 * Each tile links to the finder filtered to BOTH the tile's category AND the geo,
 * e.g. /nos-idees-de-voyage/?f=plage_cote,france
 *
 * Attributes:
 *   geo        — filter slug / catalog key for the geo (e.g. "france"). Required.
 *   keys       — comma-separated catalog keys to show (e.g. "plage_cote,nature_rando").
 *   title      — section heading.
 *   columns    — grid columns (default 4).
 *   background — "cream" / "blue-tint" / "" (default, white).
 *   link_to    — "focus" (default, /nos-idees-de-voyage/) or "full" (/ou-partir-...).
 */
function mv_shortcode_geo_catalog( array $atts ): string {
	$atts = shortcode_atts(
		[
			'geo'        => '',
			'keys'       => '',
			'title'      => '',
			'columns'    => '4',
			'background' => '',
			'link_to'    => 'focus',
		],
		$atts,
		'mv-geo-catalog'
	);

	$geo     = sanitize_key( $atts['geo'] );
	$keys    = array_filter( array_map( 'sanitize_key', explode( ',', $atts['keys'] ) ) );
	$title   = sanitize_text_field( $atts['title'] );
	$columns = max( 1, (int) $atts['columns'] );
	$link_to = in_array( $atts['link_to'], [ 'focus', 'full' ], true ) ? $atts['link_to'] : 'focus';

	if ( empty( $keys ) || '' === $geo ) {
		return '';
	}

	ob_start();
	get_template_part( 'template-parts/mv-shared/catalog-tile-grid', null, [
		'section_class' => 'mv-geo-catalog',
		'title'         => $title,
		'keys'          => $keys,
		'columns'       => $columns,
		'background'    => sanitize_html_class( $atts['background'] ),
		'link_to'       => $link_to,
		'geo_key'       => $geo,
	] );
	return ob_get_clean();
}
add_shortcode( 'mv-geo-catalog', 'mv_shortcode_geo_catalog' );
