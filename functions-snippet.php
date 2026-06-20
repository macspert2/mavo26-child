<?php
/**
 * Add this block to the child theme's functions.php.
 * Not a standalone file — there is no plugin/theme header on purpose.
 */

/**
 * Hidden homepage prototype: noindex + nofollow.
 * Prefer an SEO plugin's page-level noindex setting if available instead.
 */
add_action( 'wp_head', function () {
	if ( is_page( 'accueil-prototype' ) ) {
		echo "<meta name=\"robots\" content=\"noindex,nofollow\">\n";
	}
} );

/**
 * Hidden homepage prototype: component CSS, only on that page template.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_page_template( 'page-accueil-prototype.php' ) ) {
		wp_enqueue_style(
			'mv-home',
			get_stylesheet_directory_uri() . '/assets/css/mv-home.css',
			[],
			wp_get_theme()->get( 'Version' )
		);
	}
} );
