<?php
/**
 * Search results page polish — plan3.md. GP already renders a real
 * title (h1.page-title, "Résultats de recherche pour <span>{query}</span>")
 * via its own archive-title mechanism — confirmed live, no override
 * needed there, just CSS to add quote marks around the query (scoped to
 * body.search-results so it doesn't affect other archive titles).
 *
 * Everything else (helper text, a second search form, the orientation
 * box) is injected via generate_before_main_content, gated to
 * is_search() — same hook-based approach as the sidebar content
 * (mv-search-sidebar.php), so search.php never needed to come back into
 * the child theme.
 *
 * EN/DE have no dedicated Start Here page or destinations hub, so their
 * orientation box links to the homepage prototype directly (not
 * [travel_finder_focus], which would just redirect there anyway with no
 * ?f= argument — see TVF_Focus::maybe_redirect()) and shows a single
 * button rather than FR's two (a second, identically-targeted button
 * would be redundant).
 *
 * If the search term matches a known place name, the mavo-geotag-plus
 * plugin's geo_tagger_search_hierarchy() (guarded with function_exists,
 * since the theme shouldn't hard-depend on that plugin) replaces the
 * generic "Essayez par exemple..." suggestions line with real links to
 * that place's city/region/country tag archives — more useful than
 * generic examples when a real match exists.
 *
 * The 4 main strings (helper, suggestions, orientation title/text) are
 * editable from Réglages MaVo (mv_get_string()) — button labels stay
 * hardcoded here, that wasn't part of what got exposed. The orientation
 * box itself (title + text + buttons) can be hidden entirely via the
 * 'search_orientation_box' toggle, also in Réglages MaVo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'generate_before_main_content', 'mv_render_search_page_header' );
function mv_render_search_page_header(): void {
	if ( ! is_search() ) {
		return;
	}

	$lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : 'fr';

	$t = [
		'helper'            => mv_get_string( 'search_helper', $lang ),
		'suggestions'       => mv_get_string( 'search_suggestions', $lang ),
		'orientation_title' => mv_get_string( 'search_orientation_title', $lang ),
		'orientation_text'  => mv_get_string( 'search_orientation_text', $lang ),
	];

	// FR: 2 buttons (real Start Here page + real destinations hub).
	// EN/DE: 1 button (homepage) — no equivalent pages exist for either,
	// so a second button would just point at the same place.
	$buttons = [
		'fr' => [
			[ 'label' => 'Explorer Maman Voyage', 'url' => 'https://www.mamanvoyage.com/explorer/' ],
			[ 'label' => 'Toutes nos destinations', 'url' => 'https://www.mamanvoyage.com/nos-voyages/destinations/' ],
		],
		'en' => [
			[ 'label' => 'Explore the homepage', 'url' => 'https://www.mamanvoyage.com/en/' ],
		],
		'de' => [
			[ 'label' => 'Zur Startseite', 'url' => 'https://www.mamanvoyage.com/de/' ],
		],
	];
	$page_buttons = $buttons[ $lang ] ?? $buttons['fr'];
	?>
	<div class="mv-search-header">
		<p class="mv-search-header__helper"><?php echo esc_html( $t['helper'] ); ?></p>

		<?php get_search_form(); ?>
		<?php
		$geo_hierarchy = function_exists( 'geo_tagger_search_hierarchy' )
			? geo_tagger_search_hierarchy( get_search_query(), $lang )
			: '';
		if ( $geo_hierarchy ) {
			echo $geo_hierarchy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped at the source (GeoTagger\SearchHierarchy::render()).
		} else {
			?>
			<p class="mv-search-header__suggestions"><?php echo esc_html( $t['suggestions'] ); ?></p>
			<?php
		}
		?>

		<?php if ( mv_section_enabled( 'search_orientation_box' ) ) : ?>
		<div class="mv-search-header__orientation mv-tile mv-tile--utility">
			<h2 class="mv-search-header__orientation-title"><?php echo esc_html( $t['orientation_title'] ); ?></h2>
			<p><?php echo esc_html( $t['orientation_text'] ); ?></p>
			<div class="mv-search-header__orientation-buttons">
				<?php foreach ( $page_buttons as $button ) : ?>
					<a class="mv-button mv-button--primary" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
