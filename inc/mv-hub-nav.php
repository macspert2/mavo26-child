<?php
/**
 * Automatic hub navigation strip on single posts — hub-strip.md.
 *
 * Renders, after the complete comments area and inside .site-main, a compact
 * strip linking to the post's primary geographic and thematic hubs.
 *
 * The relationships are owned by the Mavo Hub Manager plugin; this module only
 * reads them through its stable procedural API (mavo_get_primary_hub(),
 * mavo_get_hub_type()) and never touches the three meta keys directly. With
 * the plugin inactive nothing renders and nothing fatals.
 *
 * Note this is a second hub strip: mavo-custom-shortcodes' [mavo_hub_strip]
 * renders the same relationships manually, in post content, with its own label
 * and markup. Hub Manager's "No link back to hub" audit recognises that
 * shortcode by parsing post_content, so it cannot see this template-level
 * strip — its link-back filters take shortcode tags, not template hooks.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Strip label for the current language, per Polylang, with a locale fallback.
 * Unsupported languages fall back to French, as elsewhere in this theme.
 */
function mv_hub_nav_label(): string {
	$language = function_exists( 'pll_current_language' )
		? pll_current_language( 'slug' )
		: '';

	if ( ! is_string( $language ) || '' === $language ) {
		$language = get_locale();
	}

	$parts = preg_split( '/[-_]/', strtolower( $language ) );

	$labels = [
		'fr' => 'Continuer à explorer',
		'en' => 'Continue exploring',
		'de' => 'Weiter entdecken',
	];

	return $labels[ $parts[0] ] ?? $labels['fr'];
}

/**
 * Resolve one usable hub link for a post, or null.
 *
 * mavo_get_primary_hub() returns the raw stored ID and its own docblock says
 * it may be stale, so every condition below is checked here: the target must
 * exist, be published, be unprotected, still carry the expected hub type, not
 * be the post itself, and — since a hub in another language is a Hub Manager
 * diagnostic rather than a usable link — share the post's language.
 *
 * @return array{id:int,type:string,title:string,url:string}|null
 */
function mv_hub_nav_target( int $post_id, string $type ): ?array {
	if ( ! in_array( $type, [ 'geo', 'theme' ], true ) ) {
		return null;
	}

	if ( ! function_exists( 'mavo_get_primary_hub' ) || ! function_exists( 'mavo_get_hub_type' ) ) {
		return null; // Mavo Hub Manager inactive.
	}

	$hub_id = (int) mavo_get_primary_hub( $post_id, $type );

	if ( ! $hub_id || $hub_id === $post_id ) {
		return null;
	}

	$hub = get_post( $hub_id );

	if ( ! $hub instanceof WP_Post
		|| 'publish' !== $hub->post_status
		|| '' !== $hub->post_password
		|| $type !== mavo_get_hub_type( $hub_id )
	) {
		return null;
	}

	// Cross-language relationships are reported by the audit, not followed here.
	if ( class_exists( 'MHM_Model' ) && ! MHM_Model::is_same_language( $post_id, $hub_id ) ) {
		return null;
	}

	$title = trim( wp_strip_all_tags( (string) get_the_title( $hub_id ) ) );
	$url   = (string) get_permalink( $hub_id );

	if ( '' === $title || '' === $url || '' === esc_url( $url ) ) {
		return null;
	}

	return [
		'id'    => $hub_id,
		'type'  => $type,
		'title' => $title,
		'url'   => $url,
	];
}

/**
 * The post's usable hub links, geo before theme, deduplicated by target ID.
 *
 * Resolved once per request: the enqueue callback needs the answer before the
 * template runs, so the stylesheet is only requested when a strip will exist.
 *
 * @return array<int, array{id:int,type:string,title:string,url:string}>
 */
function mv_hub_nav_links( int $post_id ): array {
	static $cache = [];

	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$links = [];
	$seen  = [];

	foreach ( [ 'geo', 'theme' ] as $type ) {
		$hub = mv_hub_nav_target( $post_id, $type );

		if ( $hub && ! isset( $seen[ $hub['id'] ] ) ) {
			$links[]             = $hub;
			$seen[ $hub['id'] ]  = true;
		}
	}

	$cache[ $post_id ] = $links;

	return $links;
}

/** Single posts only, and never on a post the visitor has not unlocked. */
function mv_hub_nav_current_post_id(): int {
	if ( ! is_singular( 'post' ) ) {
		return 0;
	}

	$post_id = (int) get_queried_object_id();

	if ( ! $post_id || post_password_required( $post_id ) ) {
		return 0;
	}

	return $post_id;
}

/**
 * Component CSS, requested only on posts that actually render a strip.
 * Same conditional pattern as mv-home.css in functions.php.
 */
add_action( 'wp_enqueue_scripts', function () {
	$post_id = mv_hub_nav_current_post_id();

	if ( ! $post_id || ! mv_hub_nav_links( $post_id ) ) {
		return;
	}

	wp_enqueue_style(
		'mv-hub-nav',
		get_stylesheet_directory_uri() . '/assets/css/mv-hub-nav.css',
		[],
		mv_asset_version( 'assets/css/mv-hub-nav.css' )
	);
} );

/**
 * generate_after_main_content fires inside .site-main, after the loop and
 * after comments_template() — see GeneratePress single.php. It is therefore
 * reached on posts with comments, without comments, and with comments closed
 * alike. Nothing else in this child theme hooks it.
 */
add_action( 'generate_after_main_content', 'mv_render_hub_nav', 20 );

function mv_render_hub_nav(): void {
	$post_id = mv_hub_nav_current_post_id();

	if ( ! $post_id ) {
		return;
	}

	$links = mv_hub_nav_links( $post_id );

	if ( ! $links ) {
		return;
	}

	$label = mv_hub_nav_label();
	?>
	<nav class="mv-hub-nav" aria-label="<?php echo esc_attr( $label ); ?>">
		<span class="mv-hub-nav__label"><?php echo esc_html( $label ); ?></span>
		<ul class="mv-hub-nav__links">
			<?php foreach ( $links as $hub ) : ?>
				<li class="mv-hub-nav__item">
					<a class="mv-hub-nav__hub mv-hub-nav__hub--<?php echo esc_attr( $hub['type'] ); ?>"
					   href="<?php echo esc_url( $hub['url'] ); ?>">
						<span class="mv-hub-nav__icon" aria-hidden="true">
							<?php if ( 'geo' === $hub['type'] ) : ?>
								<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M12 21s7-7 7-12a7 7 0 1 0-14 0c0 5 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>
							<?php else : ?>
								<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m16 8-2.5 5.5L8 16l2.5-5.5Z"/></svg>
							<?php endif; ?>
						</span>
						<span class="mv-hub-nav__text"><?php echo esc_html( $hub['title'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}
