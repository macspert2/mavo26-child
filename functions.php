<?php

if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
}

/**
 * Cache-busting version for a child-theme asset, from its mtime.
 *
 * mtime rather than the theme version, because the theme version only changes
 * when someone remembers to bump it — which is exactly the moment a CSS edit
 * silently fails to reach anyone.
 *
 * Defined once because the fallback kept being forgotten: three call sites
 * guarded with file_exists() and three called filemtime() bare, where a missing
 * file means a PHP warning and `false` as the version.
 *
 * @param string $relative Path inside the child theme, e.g. 'assets/css/mv-home.css'.
 */
function mv_asset_version( string $relative ) {
	$path = get_stylesheet_directory() . '/' . ltrim( $relative, '/' );

	return file_exists( $path ) ? filemtime( $path ) : wp_get_theme()->get( 'Version' );
}

/**
 * An absolute URL on this site for a path that already carries its own
 * language prefix (e.g. '/a-propos/', '/en/about/').
 *
 * Deliberately not home_url(). Polylang runs in directory mode here and filters
 * home_url(), so on an English page it can return the English home — which
 * would turn '/a-propos/' into '/en/a-propos/' and break links that are
 * *meant* to point at the French page. get_option('home') is the configured
 * site address before any of that, and set_url_scheme() keeps http/https
 * correct.
 *
 * For "this language's front page", use pll_home_url() instead — that is the
 * question Polylang exists to answer.
 */
function mv_site_url( string $path = '/' ): string {
	$base = untrailingslashit( (string) get_option( 'home' ) );

	return set_url_scheme( $base . '/' . ltrim( $path, '/' ) );
}

require_once get_stylesheet_directory() . '/inc/mv-settings.php';
require_once get_stylesheet_directory() . '/inc/mv-landing-footer.php';
require_once get_stylesheet_directory() . '/inc/mv-search-page.php';
require_once get_stylesheet_directory() . '/inc/mv-badges.php';
require_once get_stylesheet_directory() . '/inc/mv-geo-hub-shortcodes.php';
require_once get_stylesheet_directory() . '/inc/mv-hub-nav.php';
if ( is_admin() ) {
	require_once get_stylesheet_directory() . '/inc/mv-badges-admin.php';
	require_once get_stylesheet_directory() . '/inc/mv-geo-hub-admin.php';
}

add_theme_support('post-thumbnails');
add_image_size('medium-responsive', 640, 0, false);
add_image_size('small-responsive', 480, 0, false);
remove_image_size('1536x1536');
remove_image_size('2048x2048');

add_filter('mce_css', function ($mce_css) {
    $editor_css_url = get_stylesheet_directory_uri() . '/editor-style.css';
    $editor_css_path = get_stylesheet_directory() . '/editor-style.css';

    $version = file_exists($editor_css_path)
        ? filemtime($editor_css_path)
        : time();

    $editor_css_url = add_query_arg('ver', $version, $editor_css_url);

    if (!empty($mce_css)) {
        $mce_css .= ',';
    }

    $mce_css .= $editor_css_url;

    return $mce_css;
});
function mytheme_change_tinymce_colors( $init ) {
    $default_colours = '
        "000000", "Black",
        "993300", "Burnt orange",
        "333300", "Dark olive",
        "003300", "Dark green",
        "003366", "Dark azure",
        "000080", "Navy Blue",
        "333399", "Indigo",
        "333333", "Very dark gray",
        "800000", "Maroon",
        "FF6600", "Orange",
        "808000", "Olive",
        "008000", "Green",
        "008080", "Teal",
        "0000FF", "Blue",
        "666699", "Grayish blue",
        "808080", "Gray",
        "FF0000", "Red",
        "FF9900", "Amber",
        "99CC00", "Yellow green",
        "339966", "Sea green",
        "33CCCC", "Turquoise",
        "3366FF", "Royal blue",
        "800080", "Purple",
        "999999", "Medium gray",
        "FF00FF", "Magenta",
        "FFCC00", "Gold",
        "FFFF00", "Yellow",
        "00FF00", "Lime",
        "00FFFF", "Aqua",
        "00CCFF", "Sky blue",
        "993366", "Brown",
        "C0C0C0", "Silver",
        "FF99CC", "Pink",
        "FFCC99", "Peach",
        "FFFF99", "Light yellow",
        "CCFFCC", "Pale green",
        "CCFFFF", "Pale cyan",
        "99CCFF", "Light sky blue",
        "CC99FF", "Plum",
        "FFFFFF", "White"
        ';
    $custom_colours = '
        "4e74a5", "link blue",
        "886353", "title brown",
        "5c3d2e", "darker brown",
        "a92d87", "purple highlight",
        "757575", "light grey"
        ';
    $init['textcolor_map'] = '['.$default_colours.','.$custom_colours.']';
    $init['textcolor_rows'] = 6; // expand colour grid to 6 rows
    return $init;
}
add_filter('tiny_mce_before_init', 'mytheme_change_tinymce_colors');

add_filter( 'mce_buttons_2', function ( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
});
add_filter('tiny_mce_before_init', function ($init) {
    $style_formats = array(
        // Each array child is a format with it's own settings - add as many as you want
        array(
            'title'    => 'purple h2',
            'block' => 'h2', // Element to target in editor
            'classes'  => 'mv-highlight' // Class name used for CSS
        ),
        array(
            'title'    => 'purple h3',
            'block' => 'h3', // Element to target in editor
            'classes'  => 'mv-highlight' // Class name used for CSS
        ),
        array(
            'title'    => 'purple h4',
            'block' => 'h4', // Element to target in editor
            'classes'  => 'mv-highlight' // Class name used for CSS
        ),
    );
    $init['style_formats'] = json_encode( $style_formats );
    return $init;
} );

/*
 * SVG uploads used to be enabled here (an upload_mimes filter adding
 * image/svg+xml). Removed: WordPress does not sanitise SVG, and an SVG can
 * carry <script>, an onload attribute or a foreignObject, so an uploaded one
 * is served from this origin as executable markup. Nothing in the theme
 * referenced an uploaded .svg — every icon on the site is inline SVG in a
 * template — so the capability was unused.
 *
 * If SVG uploads are ever needed again, add sanitising on upload rather than
 * re-adding the mime type on its own.
 */

/**
 * Page-specific component CSS: homepage variants, explorer, and search results.
 * Not loaded on post/archive pages where none of these components appear.
 */
add_action( 'wp_enqueue_scripts', function () {
    // mv_is_landing_page() (inc/mv-landing-footer.php, required above) owns the
    // list of landing-page slugs. It was spelled out a second time here, in a
    // different order, which is how two copies of one list start disagreeing.
    if ( mv_is_landing_page() || is_search() ) {
        wp_enqueue_style(
            'mv-home',
            get_stylesheet_directory_uri() . '/assets/css/mv-home.css',
            [],
            mv_asset_version( 'assets/css/mv-home.css' )
        );
    }
    wp_enqueue_style(
        'mavo-tiles',
        get_stylesheet_directory_uri() . '/assets/css/mv-tiles.css',
        [],
        mv_asset_version( 'assets/css/mv-tiles.css' )
    );
} );

/**
 * The comment submit button carries .mv-stamp (see mv-custom.css).
 *
 * Through comment_form_defaults rather than by overriding the template: the
 * button is rendered by WordPress core's comment_form(), and this is the seam
 * core provides for exactly this.
 */
add_filter( 'comment_form_defaults', function ( $defaults ) {
    $defaults['class_submit'] = trim( ( $defaults['class_submit'] ?? 'submit' ) . ' mv-stamp' );

    return $defaults;
} );

/**
 * Site CSS, moved out of the Customizer (assets/css/mv-custom.css).
 *
 * Enqueued at priority 999 so it prints after every other stylesheet, because
 * the Customizer printed its CSS at wp_head 101 — after every enqueued
 * stylesheet on the page. These rules only work because they come last, so the
 * move has to preserve that or the site changes.
 *
 * Two consequences worth knowing:
 *   - A plugin that enqueues at a priority above 999 would now come after this
 *     file, where it used to come before the Customizer. None currently does.
 *   - Once the global rules in that file are scoped properly (the `a:hover`
 *     one especially), loading last stops mattering and this can go back to a
 *     normal priority.
 */
add_action( 'wp_enqueue_scripts', function () {
    $path = get_stylesheet_directory() . '/assets/css/mv-custom.css';

    if ( ! file_exists( $path ) ) {
        return;
    }

    // No dependency on purpose: WordPress silently drops a stylesheet whose
    // dependency is not registered, and priority 999 already appends this to
    // the end of the queue, which is where it prints from.
    wp_enqueue_style(
        'mv-custom',
        get_stylesheet_directory_uri() . '/assets/css/mv-custom.css',
        [],
        filemtime( $path )
    );
}, 999 );

/**
 * Search results: enable the sidebar (generate_sidebar_layout filter,
 * see inc/theme-functions.php in GeneratePress), suppress the generic
 * fallback widgets (search box + monthly archives, see
 * generate_show_default_sidebar_widgets in inc/structure/sidebars.php),
 * and render our own content instead via the existing
 * generate_before_right_sidebar_content hook.
 */
add_filter( 'generate_sidebar_layout', function ( $layout ) {
    if ( is_search() ) {
        return 'right-sidebar';
    }
    return $layout;
} );

add_filter( 'generate_show_default_sidebar_widgets', function ( $show ) {
    if ( is_search() ) {
        return false;
    }
    return $show;
} );

add_action( 'generate_before_right_sidebar_content', function () {
    if ( is_search() ) {
        get_template_part( 'template-parts/mv-search-sidebar' );
    }
} );

/* temp fix for missing css style in wordpress 7.0 - remove later and test if connectors page works */
add_action('admin_head', function() {
    echo '<style>
        .boot-layout-container {
            min-height: calc(100vh - 32px);
            display: flex;
            flex-direction: column;
        }
    </style>';
});

/* cache-enabler: cache only pages/posts */
add_filter( 'cache_enabler_bypass_cache', function ( $bypass ) {

    if ( is_admin() ) {
        return $bypass;
    }

    if (
        is_archive()
        || is_search()
        || is_feed()
        || is_404()
    ) {
        return true;
    }

    return $bypass;
} );

// add comments to json-ld - remove microdata from comments
/* add_filter( 'wpseo_schema_graph', function( $graph, $context ) {
    if ( ! is_singular() ) return $graph;

    $comments = get_comments( [
        'post_id' => get_the_ID(),
        'status'  => 'approve',
        'number'  => 42,
    ] );

    if ( empty( $comments ) ) return $graph;

    $comment_data = array_map( function( $comment ) {
        return [
            '@type'       => 'Comment',
            'dateCreated' => get_comment_date( 'c', $comment ),
            'description' => wp_strip_all_tags( $comment->comment_content ),
	    'author' => array_filter( [
		'@type' => 'Person',
		'name'  => $comment->comment_author,
		'url'   => ! empty( $comment->comment_author_url ) ? $comment->comment_author_url : null,
	    ] ),
        ];
    }, $comments );

    // Find the Article node and inject comments into it
    foreach ( $graph as &$node ) {
        if ( isset( $node['@type'] ) && in_array( $node['@type'], [ 'Article', 'BlogPosting' ], true ) ) {
            $node['comment'] = $comment_data;
            break;
        }
    }

    return $graph;
}, 10, 2 );
add_filter( 'generate_comment-body_microdata', '__return_empty_string' ); */

// remove users from rest API
add_filter( 'rest_endpoints', 'rudr_remove_rest_api_endpoint' );
function rudr_remove_rest_api_endpoint( $rest_endpoints ) {
if( ! is_user_logged_in() ) {
    if( isset( $rest_endpoints[ '/wp/v2/users' ] ) ) {
        unset( $rest_endpoints[ '/wp/v2/users' ] );
    }
    if( isset( $rest_endpoints[ '/wp/v2/users/(?P<id>[\d]+)' ] ) ) {
        unset( $rest_endpoints[ '/wp/v2/users/(?P<id>[\d]+)' ] );
    }
}
return $rest_endpoints;
}

/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'generate-child' );
}, 50 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

add_action( 'after_setup_theme','mavo_remove_header' );
function mavo_remove_header() {
    remove_action( 'generate_header','generate_construct_header' );
}
add_filter( 'generate_copyright','tu_custom_copyright' );
function tu_custom_copyright() {
	// wp_date, not date: date() reads the server's timezone, so for the few
	// hours either side of New Year the footer showed the wrong year.
	echo 'Copyright &copy; ' . esc_html( wp_date( 'Y' ) ) . '&nbsp;';
	bloginfo('name');
}
add_filter( 'generate_font_display', function() {
    // You can change 'swap' to 'optional' if preferred
    return 'swap';
} );
function wpjp_dequeue_script() {
    wp_dequeue_script( 'wp-polyfill' );
}
add_action( 'wp_print_scripts', 'wpjp_dequeue_script', 100 );


function theme_shortcode_blockquote($atts, $content = null, $code = '') {
/*if ( is_feed() )
return '';
        return '<blockquote class="alignright">' . $content . '</blockquote>';
*/
return '';
}
add_shortcode('blockquote', 'theme_shortcode_blockquote');


/**
 * [mv-tile-grid] … [/mv-tile-grid]
 * Enclosing shortcode: wraps inner [mv-tile] shortcodes in a responsive
 * overlay tile grid (2-col desktop, 1-col mobile). Matches archive page style.
 *
 * Optional: columns="3" forces an explicit column count via mv-grid--N.
 */
function mv_shortcode_tile_grid( $atts, $content = null ) {
	$atts      = shortcode_atts( [ 'columns' => '' ], $atts, 'mv-tile-grid' );
	$col_class = $atts['columns'] ? ' mv-grid--' . absint( $atts['columns'] ) : '';
	// wpautop runs before shortcodes and turns newlines between [mv-tile] lines
	// into <br> / <p> tags that would become stray grid children. Strip them first.
	$clean = preg_replace( '/<\/?p[^>]*>|<br\s*\/?>/', '', $content ?? '' );
	return '<div class="mv-tile-grid mv-archive-grid mv-archive-grid--wide' . esc_attr( $col_class ) . '">'
		. do_shortcode( $clean )
		. '</div>';
}
add_shortcode( 'mv-tile-grid', 'mv_shortcode_tile_grid' );

/**
 * [mv-tile title="…" url="…" image="…" description="…"]
 * A single overlay tile. Intended for use inside [mv-tile-grid].
 *
 * url:   full URL, absolute path, or slug relative to home (e.g. "tag/berlin/")
 * image: full URL, or path relative to /wp-content/uploads/ (e.g. "2020/07/photo.jpeg")
 */
function mv_shortcode_tile( $atts ) {
	$atts = shortcode_atts(
		[
			'title'       => '',
			'url'         => '',
			'link'        => '', // legacy alias for url
			'image'       => '',
			'description' => '',
		],
		$atts,
		'mv-tile'
	);

	$title       = sanitize_text_field( $atts['title'] );
	$description = sanitize_text_field( $atts['description'] );

	if ( '' === $title ) {
		return '';
	}

	$raw_url = $atts['url'] ?: $atts['link'];
	$url     = ( str_starts_with( $raw_url, 'http' ) || str_starts_with( $raw_url, '/' ) )
		? $raw_url
		: home_url( '/' . ltrim( $raw_url, '/' ) );

	$raw_img = $atts['image'];
	if ( $raw_img && ! str_starts_with( $raw_img, 'http' ) && ! str_starts_with( $raw_img, '/' ) ) {
		$raw_img = content_url( 'uploads/' . $raw_img );
	}

	$post_id    = url_to_postid( $url );
	$tile_class = 'mv-tile mv-tile--overlay' . ( $raw_img ? '' : ' mv-tile--no-media' );

	$output  = '<div class="' . esc_attr( $tile_class ) . '">';
	if ( $raw_img ) {
		$output .= '<span class="mv-tile__media">'
			. '<img class="mv-tile__img" src="' . esc_url( $raw_img ) . '" alt="" loading="lazy" decoding="async">'
			. '</span>';
	}
	$output .= '<span class="mv-tile__body">';
	if ( $post_id > 0 && function_exists( 'mv_tile_badges' ) ) {
		$badge_args = [ 'context' => 'geo_hub', 'limit' => 2 ];
		$page_geo   = function_exists( 'mv_page_current_geo' ) ? mv_page_current_geo() : null;
		if ( $page_geo ) {
			$badge_args['current_geo'] = $page_geo;
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$output .= mv_tile_badges( $post_id, $badge_args );
	}
	$output .= '<span class="mv-tile__title">'
		. '<a class="mv-tile__link" href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>'
		. '</span>'
		. ( $description ? '<span class="mv-tile__description">' . esc_html( $description ) . '</span>' : '' )
		. '</span>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'mv-tile', 'mv_shortcode_tile' );

/**
 * [mv-box type="info|tip|related" title="..."]content[/mv-box]
 * In-article callout box. Types: info (blue tint), tip (warm tint), related (white + border).
 */
function mv_shortcode_box( $atts, $content = null ) {
	$atts  = shortcode_atts( [ 'type' => 'info', 'title' => '' ], $atts, 'mv-box' );
	$type  = sanitize_html_class( $atts['type'] );
	$class = 'mv-tile mv-tile--utility mv-tile--utility--' . $type;
	$html  = '<div class="' . esc_attr( $class ) . '">';
	if ( $atts['title'] ) {
		$html .= '<strong class="mv-box__title">' . esc_html( $atts['title'] ) . '</strong>';
	}
	$html .= do_shortcode( $content );
	$html .= '</div>';
	return $html;
}
add_shortcode( 'mv-box', 'mv_shortcode_box' );

/* tagcards not used anymore
function theme_shortcode_tagcards($atts, $content = null, $code = '') {
    $atts = shortcode_atts(
        [
            'tag'   => '',
            'count' => 4,
	    'exclude' => 0,
        ],
        $atts,
        'tagcards-inc-ul'
    );

    $tag   = sanitize_text_field( $atts['tag'] );
    $count = absint( $atts['count'] ) + 1;
    $excl  = absint( $atts['exclude'] );

    if ( empty( $tag ) || $count < 1 ) {
        return '';
    }

    $query = new WP_Query( [
        'tag'            => $tag,
        'posts_per_page' => $count,
        'meta_key'       => 'views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    ] );

    if ( ! $query->have_posts() ) {
        return '';
    }

    $current_geo = null;
    if ( function_exists( 'mv_current_geo_from_term' ) ) {
        $tag_term = get_term_by( 'slug', $tag, 'post_tag' );
        if ( $tag_term instanceof \WP_Term ) {
            $current_geo = mv_current_geo_from_term( $tag_term );
        }
    }

    $output     = '<div class="mv-tile-grid mv-archive-grid mv-archive-grid--wide">';
    $badge_seen = [];

    while ( $query->have_posts() ) {
        $query->the_post();
        if ( ( get_the_id() * 1 ) !== $excl && $count > 1 ) {
            $count--;
            $thumb_url  = get_the_post_thumbnail_url( null, 'large' );
            $tile_class = 'mv-tile mv-tile--overlay' . ( $thumb_url ? '' : ' mv-tile--no-media' );
            $excerpt    = wp_trim_words( get_the_excerpt(), 22 );

            $output .= '<div class="' . esc_attr( $tile_class ) . '">';
            if ( $thumb_url ) {
                $output .= '<span class="mv-tile__media">'
                    . '<img class="mv-tile__img" src="' . esc_url( $thumb_url ) . '" alt="" loading="lazy" decoding="async">'
                    . '</span>';
            }
            $output .= '<span class="mv-tile__body">';
            if ( function_exists( 'mv_get_tile_badges' ) ) {
                $badge_args = [ 'context' => 'geo_hub', 'limit' => 2, 'seen_labels' => $badge_seen ];
                if ( $current_geo ) {
                    $badge_args['current_geo'] = $current_geo;
                }
                $badges = mv_get_tile_badges( get_the_ID(), $badge_args );
                mv_badges_update_seen( $badge_seen, $badges );
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $output .= mv_render_tile_badges( $badges, $badge_args );
            }
            $output .= '<span class="mv-tile__title">'
                . '<a class="mv-tile__link" href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>'
                . '</span>'
                . ( $excerpt ? '<span class="mv-tile__description">' . esc_html( $excerpt ) . '</span>' : '' )
                . '</span>';
            $output .= '</div>';
        }
    }

    wp_reset_postdata();

    $output .= '</div>';
    return $output;
}
add_shortcode('tagcards-inc-ul', 'theme_shortcode_tagcards'); */

/* catcards still used in one post */
function theme_shortcode_catcards($atts, $content = null, $code = '') {
    $atts = shortcode_atts(
        [
            'cat'   => '',
            'count' => 4,
            'exclude' => 0,
        ],
        $atts,
        'catcards-inc-ul'
    );

    $cat   = sanitize_text_field( $atts['cat'] );
    $count = round( (float) $atts['count'] );
    $excl  = absint( $atts['exclude'] );

    if ( empty( $cat )) {
        return '';
    }

    $query = new WP_Query( [
        'category_name'       => $cat,
        'posts_per_page' => $count,
        'post_type'      => 'post',
        'orderby'        => 'date',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ] );

    if ( ! $query->have_posts() ) {
        return '0';
    }

    $output     = '<div class="mv-tile-grid mv-archive-grid mv-archive-grid--wide">';
    $badge_seen = [];

    while ( $query->have_posts() ) {
        $query->the_post();
        if ( ( get_the_id() * 1 ) !== $excl ) {
            $thumb_url  = get_the_post_thumbnail_url( null, 'large' );
            $tile_class = 'mv-tile mv-tile--overlay' . ( $thumb_url ? '' : ' mv-tile--no-media' );
            $excerpt    = wp_trim_words( get_the_excerpt(), 22 );

            $output .= '<div class="' . esc_attr( $tile_class ) . '">';
            if ( $thumb_url ) {
                $output .= '<span class="mv-tile__media">'
                    . '<img class="mv-tile__img" src="' . esc_url( $thumb_url ) . '" alt="" loading="lazy" decoding="async">'
                    . '</span>';
            }
            $output .= '<span class="mv-tile__body">';
            if ( function_exists( 'mv_get_tile_badges' ) ) {
                $badge_args = [ 'context' => 'geo_hub', 'limit' => 2, 'seen_labels' => $badge_seen ];
                $page_geo   = function_exists( 'mv_page_current_geo' ) ? mv_page_current_geo() : null;
                if ( $page_geo ) {
                    $badge_args['current_geo'] = $page_geo;
                }
                $badges = mv_get_tile_badges( get_the_ID(), $badge_args );
                mv_badges_update_seen( $badge_seen, $badges );
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $output .= mv_render_tile_badges( $badges, $badge_args );
            }
            $output .= '<span class="mv-tile__title">'
                . '<a class="mv-tile__link" href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>'
                . '</span>'
                . ( $excerpt ? '<span class="mv-tile__description">' . esc_html( $excerpt ) . '</span>' : '' )
                . '</span>';
            $output .= '</div>';
        }
    }

    wp_reset_postdata();

    $output .= '</div>';
    return $output;
}
add_shortcode('catcards-inc-ul', 'theme_shortcode_catcards');

/**
 * Hold a post back from the feeds for its first few minutes, so a publish that
 * is immediately corrected does not go out in the first state.
 *
 * Three things were wrong with the version this replaces, all of them the
 * defaults of the snippet it came from:
 *
 *   - it tested the global is_feed() and took only $where, so it could not tell
 *     the feed's own query from any other query running on a feed request, and
 *     applied the clause to all of them;
 *   - it concatenated its values into SQL instead of preparing them. Nothing
 *     here is user input, so it was not injectable — but this is the shape
 *     people copy;
 *   - TIMESTAMPDIFF(MINUTE, post_date_gmt, now) > 10 cannot use the post_date
 *     index. Comparing the column against a cutoff computed in PHP is the same
 *     question asked so that MySQL can answer it from the index — which the
 *     feeds need more than most pages, since cache_enabler_bypass_cache()
 *     deliberately leaves them uncached.
 */
function publish_later_on_feed( $where, $query = null ) {
        global $wpdb;

        if ( ! $query instanceof WP_Query || ! $query->is_feed() || ! $query->is_main_query() ) {
                return $where;
        }

        $hold_minutes = (int) apply_filters( 'mavo_feed_hold_minutes', 10 );

        if ( $hold_minutes < 1 ) {
                return $where;
        }

        $cutoff = gmdate( 'Y-m-d H:i:s', time() - ( $hold_minutes * MINUTE_IN_SECONDS ) );

        return $where . $wpdb->prepare( " AND {$wpdb->posts}.post_date_gmt < %s ", $cutoff );
}
add_filter( 'posts_where', 'publish_later_on_feed', 10, 2 );

//truncate feed at more tag
function mytheme_content_feed($feed_type = null) {
        if ( !$feed_type )
                $feed_type = get_default_feed();
        global $more;
        $more_restore = $more;
        $more = 0;
        $content = apply_filters('the_content', get_the_content());
        $more = $more_restore;
        $content = str_replace(']]>', ']]&gt;', $content);
        return $content;
}
add_filter('the_content_feed', 'mytheme_content_feed');

//disable image captions
//add_filter( 'disable_captions', create_function('$a', 'return true;') );
add_filter( 'disable_captions', '__return_true' );

add_filter( 'the_content', 'attachment_image_link_remove_filter' );
 function attachment_image_link_remove_filter( $content ) {
  $content =
  preg_replace(
  array('{<a(.*?)(wp-att|wp-content/uploads)[^>]*><img}',
  '{ wp-image-[0-9]*" /></a>}'),
  array('<img','" />'),
  $content
  );
  return $content;
}

function jetpackme_exclude_posts_subscriptions( $categories ) {
    $categories = array( 'English', 'english', 'Récap', 'recap', 'deutsch', 'deutsch' );
    return $categories;
}
add_filter( 'jetpack_subscriptions_exclude_these_categories', 'jetpackme_exclude_posts_subscriptions' );

/**
 * The category kept out of the RSS/Atom feeds.
 *
 * Was a bare '-8467' inline, with nothing saying what that category is — so
 * nobody could tell whether it was still the right one, and deleting or
 * merging the category would have silently stopped the exclusion.
 *
 * Verify with: wp term get category MAVO_FEED_EXCLUDED_CATEGORY
 * (or Posts → Categories, hovering the row shows tag_ID= in the edit link).
 *
 * Filterable so the ID can be corrected without editing the theme.
 */
if ( ! defined( 'MAVO_FEED_EXCLUDED_CATEGORY' ) ) {
    define( 'MAVO_FEED_EXCLUDED_CATEGORY', 8467 );
}

function exclude_category($query) {
    if ( $query->is_feed() ) {
        $feed = $query->get('feed');
        // Only the real RSS/Atom feeds — NOT the plugin's sitemap feeds
        if ( in_array( $feed, array('feed','rdf','rss','rss2','atom'), true ) ) {
            $excluded = absint( apply_filters( 'mavo_feed_excluded_category', MAVO_FEED_EXCLUDED_CATEGORY ) );

            if ( $excluded ) {
                $query->set( 'cat', '-' . $excluded );
            }
        }
    }
    return $query;
}
add_filter('pre_get_posts', 'exclude_category');

/**
 * Remove Ancient Custom Fields metabox from post editor
 * because it uses a very slow query meta_key sort query
 * so on sites with large postmeta tables it is super slow
 * and is rarely useful anymore on any site
 */
function s9_remove_post_custom_fields_metabox() {
     foreach ( get_post_types( '', 'names' ) as $post_type ) {
         remove_meta_box( 'postcustom' , $post_type , 'normal' );
     }
}
add_action( 'admin_menu' , 's9_remove_post_custom_fields_metabox' );

add_filter( 'relevanssi_orderby', 'rlv_fix_order' );
function rlv_fix_order( $orderby ) {
    $orderby = 'relevance';
    return $orderby;
}

function print_menu_shortcode($atts, $content = null) {
extract(shortcode_atts(array( 'name' => null, 'class' => null ), $atts));
return wp_nav_menu( array( 'menu' => $name, 'menu_class' => 'myclass', 'echo' => false ) );
}
add_shortcode('menu', 'print_menu_shortcode');

function skip_lazy_class_first_featured_image($attr) {
  global $wp_query;
  if ( 0 == $wp_query->current_post ) {
      $attr['class'] .= ' skip-lazy';
  }
  return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'skip_lazy_class_first_featured_image' );

function bl_shortcode(){
$sco ='';
    if ( is_user_logged_in() ) {
$the_query = new WP_Query('posts_per_page=-1');
while ($the_query->have_posts())
{
    $the_query->the_post();
    $ptitle = get_the_title();
    $_post_id = get_the_id();
    $pdate = get_the_date();
    $_post_content = get_post_field( 'post_content', $_post_id);
    $regex = '/https?\:\/\/[^\" ]+/i';
/*    $regex = '/<a?.+?>/i'; */
    preg_match_all($regex, $_post_content, $matches);
    $urls = $matches[0];
    foreach($urls as $url)
    {
/*        if (str_contains($url, "https://www.booking.com/") && !str_contains($url, "sponsor")) */
/*        if (!str_contains($url, "https://www.mamanvoyage.com/") && !str_contains($url, "https://www.booking.com/"))  */
        if (str_contains($url, "https://www.mamanvoyage.com/") && !str_contains($url, "/uploads/"))
/*        if (str_contains($url, "https://www.airbnb")) */
            $sco = $sco . $pdate . '§' . $_post_id . '§' . $ptitle . '§' . htmlentities($url) . '<br />';
    }

}
}
 return $sco;
}
add_shortcode('booking_links','bl_shortcode');

abstract class mavo_bpul_box {
	public static function add() {
		add_meta_box(
			'mavo_bpul_id',          // Unique ID
			'BPUL', // Box title
			[ self::class, 'html' ],   // Content callback, must be of type callable
			'post'                  // Post type
		);
	}
	public static function save( int $post_id ) {
                // Security checks
                if (!isset($_POST['mavo_bpul_box_nonce'])) return;
                if (!wp_verify_nonce($_POST['mavo_bpul_box_nonce'], 'mavo_bpul_box')) return;
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if ( array_key_exists( 'bpul_field', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_mavo_bpul_key',
				sanitize_text_field($_POST['bpul_field'])
			);
		}
	}
	public static function html( $post ) {
		wp_nonce_field('mavo_bpul_box', 'mavo_bpul_box_nonce');
                $value = get_post_meta( $post->ID, '_mavo_bpul_key', true );
		?>
		<label for="bpul_field">Booking pop-under link: </label>
		<input type="text" name="bpul_field" id="bpul_field" value="<?php echo esc_attr($value); ?>" class="postbox">
		<?php
	}
}
add_action( 'add_meta_boxes', [ 'mavo_bpul_box', 'add' ] );
add_action( 'save_post', [ 'mavo_bpul_box', 'save' ] );

abstract class mavo_maj_box {
        public static function add() {
                add_meta_box(
                        'mavo_maj_id',          // Unique ID
                        'MAJ', // Box title
                        [ self::class, 'html' ],   // Content callback, must be of type callable
                        'post'                  // Post type
                );
        }
        public static function save( int $post_id ) {
                // Security checks
                if (!isset($_POST['mavo_maj_box_nonce'])) return;
                if (!wp_verify_nonce($_POST['mavo_maj_box_nonce'], 'mavo_maj_box')) return;
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
                if ( array_key_exists( 'maj_field', $_POST ) ) {
                        update_post_meta(
                                $post_id,
                                '_mavo_maj_key',
                                sanitize_text_field($_POST['maj_field'])
                        );
                }
        }
        public static function html( $post ) {
                wp_nonce_field('mavo_maj_box', 'mavo_maj_box_nonce');
                $value = get_post_meta( $post->ID, '_mavo_maj_key', true );
                ?>
                <label for="maj_field">Date de mise à jour (mois et année): </label>
                <input type="text" name="maj_field" id="maj_field" value="<?php echo esc_attr($value); ?>" class="postbox">
                <?php
        }
}
add_action( 'add_meta_boxes', [ 'mavo_maj_box', 'add' ] );
add_action( 'save_post', [ 'mavo_maj_box', 'save' ] );

// FR affiliate script switch: false = Stay22 letmeallez, true = bpul (same rules as EN/DE).
if ( ! defined( 'MAVO_FR_USE_BPUL' ) ) {
    define( 'MAVO_FR_USE_BPUL', false );
}

function bpul_script() {
    global $wp_query;
    $post_id = $wp_query->get_queried_object_id();
    if ( ! $post_id || is_user_logged_in() ) {
        return;
    }

if ( has_category( [ 'expatriation-angleterre', 'accessoires-voyage' ], $post_id ) ) {
    return;
}

// Skip if the post isn't at least 60 hours (2.5 days) old.
    $published = get_post_timestamp( $post_id ); // GMT-based, avoids timezone drift
    if ( ! $published || ( time() - $published ) < 60 * HOUR_IN_SECONDS ) {
        return;
    }

    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'fr';

    // FR switch: false = stay22 letmeallez (current), true = bpul (same conditions as EN/DE).
    if ( 'fr' === $lang && ! MAVO_FR_USE_BPUL ) {
    wp_enqueue_script( 'stay22', 'https://scripts.stay22.com/letmeallez.js', [], null, true );
    wp_add_inline_script(
        'stay22',
        'window.Stay22 = window.Stay22 || {}; window.Stay22.params = { lmaID: "6a5a38d601f5c92f4b373772" };',
        'before'
    );
    } else {
        // EN/DE (and FR when MAVO_FR_USE_BPUL is true): only load when a $bpul URL is set for this post
        $bpul = htmlspecialchars_decode( get_post_meta( $post_id, '_mavo_bpul_key', true ) );
        if ( $bpul === '' ) {
            return;
        }
/**
 * Add Booking affiliate label dynamically.
 *
 * Label format:
 *   sanitized post slug + "_o"
 *
 * Example:
 *   wo-uebernachten-in-london-7-ideen-fuer-stadtviertel
 * becomes:
 *   wouebernachteninlondon7ideenfuerstadtviertel_o
 */
$post_slug = get_post_field( 'post_name', $post_id );
$label_base = strtolower( preg_replace( '/[^A-Za-z0-9]/', '', $post_slug ) );
$bpul_label = $label_base . '_o';

if (
    strpos( $bpul, 'aid=903703' ) !== false
    && strpos( $bpul, 'label=' ) === false
) {
    $bpul = str_replace(
        'aid=903703',
        'aid=903703&label=' . rawurlencode( $bpul_label ),
        $bpul
    );
}
        $bpul_file = get_stylesheet_directory() . '/js/bpulv5.js';
        wp_enqueue_script( 'bpul_script', get_stylesheet_directory_uri() . '/js/bpulv5.js', [], file_exists( $bpul_file ) ? filemtime( $bpul_file ) : '1.0', true );
        $script = 'const BPU_URL = atob("' . base64_encode( $bpul).'"); ';
        wp_add_inline_script( 'bpul_script', $script, 'before' );
    }
}
add_action('wp_enqueue_scripts', 'bpul_script');

//don't show pages when listing by tag
add_action( 'pre_get_posts', function( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_tag() ) {
        $query->set( 'post_type', 'post' );
    }
} );

// don't preconnect to gstatic (no google fonts needed)
add_filter( 'wp_resource_hints', function( $urls, $relation_type ) {
    if ( $relation_type !== 'preconnect' ) {
        return $urls;
    }
    foreach ( $urls as $key => $url ) {
        $href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;
        if ( str_contains( $href, 'gstatic.com' ) || str_contains( $href, 'c0.wp.com' )) {
            unset( $urls[ $key ] );
        }
    }
    return $urls;
}, 20, 2 );

// trackers are now being called later via js in mavo-cookie-banner...

// view counters GA4 and statcounter for not logged in users only
/*function add_externalcounters() {
if (!is_user_logged_in()) {
  ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-DLEB5KS5ZG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-DLEB5KS5ZG');
</script>
<!-- Statcounter code for MaVo https://www.mamanvoyage.com on Wordpress -->
<script type="text/javascript">
var sc_project=7688183;
var sc_invisible=1;
var sc_security="f196a3d7";
</script>
<script type="text/javascript" src="https://www.statcounter.com/counter/counter.js" async=""></script>
<noscript>
<div class="statcounter"><a title="Web Analytics
Made Easy - Statcounter" href="https://statcounter.com/" target="_blank" rel="noopener"><img class="statcounter" src="https://c.statcounter.com/7688183/0/f196a3d7/1/" alt="Web Analytics Made Easy - Statcounter" referrerpolicy="no-referrer-when-downgrade"></a></div>
</noscript>
<!-- End of Statcounter Code -->
<?php
}
}
add_action('wp_footer', 'add_externalcounters'); */

// Strip "Pingback: " / "Trackback: " prefix from pingback/trackback comment text.
// WP injects this prefix into comment_content; the chip layout + link icon replace it visually.
add_filter( 'comment_text', static function ( string $text, ?\WP_Comment $comment = null ): string {
	if ( $comment && in_array( $comment->comment_type, [ 'pingback', 'trackback' ], true ) ) {
		$text = preg_replace( '/^\s*(Ping|Track)back:\s*/iu', '', $text );
	}
	return $text;
}, 10, 2 );

// Tag archive H1: "Tous les articles France" instead of bare term name.
// GP calls single_term_title() directly, so we hook single_tag_title, not get_the_archive_title.
add_filter( 'single_tag_title', static function ( string $name ): string {
	$lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language( 'slug' ) : 'fr';
	$prefix = match ( $lang ) {
		'en'    => 'All articles: ',
		'de'    => 'Alle Artikel: ',
		default => 'Tous les articles ',
	};
	return $prefix . $name;
} );
