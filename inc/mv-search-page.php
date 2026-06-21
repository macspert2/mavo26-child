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

	$strings = [
		'fr' => [
			'helper'             => 'Vous cherchez une destination, une idée de week-end ou un article précis ? Essayez aussi un pays, une région ou un type de voyage.',
			'suggestions'        => 'Essayez par exemple : France, Angleterre, randonnée, bébé, ados, road trip…',
			'orientation_title'  => 'Vous ne savez pas par où commencer ?',
			'orientation_text'   => 'Notre guide « Commencez ici » vous aide à trouver des idées par destination, âge des enfants, durée, budget ou type de voyage.',
		],
		'en' => [
			'helper'             => 'Looking for a destination, a weekend idea or a specific article? Try searching for a country, region or type of trip.',
			'suggestions'        => 'Try for example: France, England, hiking, baby, teens, road trip…',
			'orientation_title'  => 'Not sure where to start?',
			'orientation_text'   => 'Visit our homepage to browse family travel ideas by destination and theme.',
		],
		'de' => [
			'helper'             => 'Sucht ihr ein Reiseziel, eine Wochenendidee oder einen bestimmten Artikel? Probiert auch ein Land, eine Region oder eine Reiseart.',
			'suggestions'        => 'Zum Beispiel: Frankreich, England, Wandern, Baby, Teenager, Roadtrip…',
			'orientation_title'  => 'Ihr wisst nicht, wo ihr anfangen sollt?',
			'orientation_text'   => 'Besucht unsere Startseite, um Reiseideen nach Reiseziel und Thema zu entdecken.',
		],
	];
	$t = $strings[ $lang ] ?? $strings['fr'];

	// FR: 2 buttons (real Start Here page + real destinations hub).
	// EN/DE: 1 button (homepage) — no equivalent pages exist for either,
	// so a second button would just point at the same place.
	$buttons = [
		'fr' => [
			[ 'label' => 'Commencez ici', 'url' => 'https://www.mamanvoyage.com/commencez-ici/' ],
			[ 'label' => 'Toutes nos destinations', 'url' => 'https://www.mamanvoyage.com/nos-voyages/destinations/' ],
		],
		'en' => [
			[ 'label' => 'Explore the homepage', 'url' => 'https://www.mamanvoyage.com/en/home-prototype/' ],
		],
		'de' => [
			[ 'label' => 'Zur Startseite', 'url' => 'https://www.mamanvoyage.com/de/startseite-prototyp/' ],
		],
	];
	$page_buttons = $buttons[ $lang ] ?? $buttons['fr'];
	?>
	<div class="mv-search-header">
		<p class="mv-search-header__helper"><?php echo esc_html( $t['helper'] ); ?></p>

		<?php get_search_form(); ?>
		<p class="mv-search-header__suggestions"><?php echo esc_html( $t['suggestions'] ); ?></p>

		<div class="mv-search-header__orientation">
			<h2 class="mv-search-header__orientation-title"><?php echo esc_html( $t['orientation_title'] ); ?></h2>
			<p><?php echo esc_html( $t['orientation_text'] ); ?></p>
			<div class="mv-search-header__orientation-buttons">
				<?php foreach ( $page_buttons as $button ) : ?>
					<a class="mv-button mv-button--secondary" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}
