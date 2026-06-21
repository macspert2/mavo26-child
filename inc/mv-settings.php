<?php
/**
 * Theme-side settings page ("Réglages MaVo") — section on/off toggles
 * for the homepage prototypes and search sidebar, a couple of tunable
 * counts, and the shared placeholder image URL. Mirrors the existing
 * mavo-travel-finder plugin's admin-post form pattern (see
 * TVF_Admin::render_sync_page()/handle_sync_translations()) rather than
 * the WP Settings API, for consistency with that established style.
 *
 * Single option (mv_theme_settings) rather than one option per field —
 * fewer autoloaded rows, and array_merge() against defaults means new
 * settings added later get sensible defaults even for sites that saved
 * the option before they existed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mv_get_settings_defaults(): array {
	return [
		'sections' => [
			'fr_trust_bar'             => true,
			'fr_primary_pathways'      => false, // matches the section's current disabled-by-default state.
			'fr_featured_destinations' => true,
			'fr_seasonal_guides'       => true,
			'fr_family_travel_themes'  => true,
			'fr_popular_last_year'     => true,
			'fr_recent_posts'          => true,
			'fr_about_mini'            => true,
			'fr_start_here_cta'        => true,

			'en_trust_bar'             => true,
			'en_destinations'          => true,
			'en_trip_type'             => true,
			'en_family_travel_themes'  => true,
			'en_recent_posts'          => true,
			'en_popular_last_year'     => true,
			'en_about_mini'            => true,

			'de_trust_bar'             => true,
			'de_destinations'          => true,
			'de_trip_type'             => true,
			'de_family_travel_themes'  => true,
			'de_recent_posts'          => true,
			'de_popular_last_year'     => true,
			'de_about_mini'            => true,

			'sidebar_search_again'     => false, // redundant since plan3.md's top-of-page search form (inc/mv-search-page.php).
			'sidebar_start_here'       => true,
			'sidebar_refine_theme'     => true,
			'sidebar_most_read'        => true,
			'sidebar_about'            => true,
			'sidebar_newsletter'       => true,
			'sidebar_social'           => true,
			'sidebar_latest_articles'  => true,

			'footer_newsletter'        => true,
		],
		'counts' => [
			'sidebar_most_read_count'       => 4,
			'sidebar_latest_articles_count' => 3,
		],
		'placeholder_image' => 'https://www.mamanvoyage.com/wp-content/uploads/2024/09/IMG_7174.jpeg',
		// 'widgets_2col' (GP's own footer-1/footer-3 widget areas, 2 columns
		// instead of 3) | 'simplified' (the original custom minimal footer,
		// kept available not deleted) | 'full' (GP's unmodified 3-column footer).
		'footer_mode' => 'widgets_2col',
	];
}

function mv_get_settings(): array {
	static $settings = null;
	if ( null === $settings ) {
		$saved    = get_option( 'mv_theme_settings', [] );
		$defaults = mv_get_settings_defaults();
		$settings = [
			'sections'          => array_merge( $defaults['sections'], $saved['sections'] ?? [] ),
			'counts'            => array_merge( $defaults['counts'], $saved['counts'] ?? [] ),
			'placeholder_image' => $saved['placeholder_image'] ?? $defaults['placeholder_image'],
			'footer_mode'       => $saved['footer_mode'] ?? $defaults['footer_mode'],
		];
	}
	return $settings;
}

function mv_section_enabled( string $key ): bool {
	$settings = mv_get_settings();
	return (bool) ( $settings['sections'][ $key ] ?? true );
}

function mv_get_setting_count( string $key, int $default ): int {
	$settings = mv_get_settings();
	return max( 1, (int) ( $settings['counts'][ $key ] ?? $default ) );
}

function mv_get_placeholder_image(): string {
	return mv_get_settings()['placeholder_image'];
}

add_action( 'admin_menu', 'mv_register_settings_page' );
function mv_register_settings_page(): void {
	add_menu_page(
		__( 'Réglages MaVo', 'mavo' ),
		__( 'Réglages MaVo', 'mavo' ),
		'manage_options',
		'mv-settings',
		'mv_render_settings_page',
		'dashicons-admin-customizer',
		31
	);
}

add_action( 'admin_post_mv_save_settings', 'mv_handle_save_settings' );
function mv_handle_save_settings(): void {
	check_admin_referer( 'mv_save_settings', 'mv_settings_nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Accès refusé.', 'mavo' ) );
	}

	$defaults = mv_get_settings_defaults();

	$sections = [];
	foreach ( array_keys( $defaults['sections'] ) as $key ) {
		$sections[ $key ] = isset( $_POST['sections'][ $key ] );
	}

	$counts = [];
	foreach ( $defaults['counts'] as $key => $default_value ) {
		$counts[ $key ] = max( 1, (int) ( $_POST['counts'][ $key ] ?? $default_value ) );
	}

	$placeholder_image = esc_url_raw( wp_unslash( $_POST['placeholder_image'] ?? $defaults['placeholder_image'] ) );

	$footer_mode = sanitize_key( wp_unslash( $_POST['footer_mode'] ?? $defaults['footer_mode'] ) );
	if ( ! in_array( $footer_mode, [ 'widgets_2col', 'simplified', 'full' ], true ) ) {
		$footer_mode = $defaults['footer_mode'];
	}

	update_option(
		'mv_theme_settings',
		[
			'sections'          => $sections,
			'counts'            => $counts,
			'placeholder_image' => $placeholder_image,
			'footer_mode'       => $footer_mode,
		]
	);

	wp_safe_redirect( add_query_arg( [ 'page' => 'mv-settings', 'mv_saved' => '1' ], admin_url( 'admin.php' ) ) );
	exit;
}

function mv_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Accès refusé.', 'mavo' ) );
	}

	$settings = mv_get_settings();

	$groups = [
		'fr' => [
			'title' => __( 'Page d’accueil — Français', 'mavo' ),
			'keys'  => [
				'fr_trust_bar'             => __( 'Bandeau de confiance', 'mavo' ),
				'fr_primary_pathways'      => __( 'Par où commencer ?', 'mavo' ),
				'fr_featured_destinations' => __( 'Destinations phares', 'mavo' ),
				'fr_seasonal_guides'       => __( 'Guides saisonniers', 'mavo' ),
				'fr_family_travel_themes'  => __( 'Voyager selon votre famille', 'mavo' ),
				'fr_popular_last_year'     => __( 'Articles populaires (l’an dernier / actuellement)', 'mavo' ),
				'fr_recent_posts'          => __( 'Derniers articles', 'mavo' ),
				'fr_about_mini'            => __( 'Qui se cache derrière Maman Voyage', 'mavo' ),
				'fr_start_here_cta'        => __( 'Appel à l’action « Commencez ici »', 'mavo' ),
			],
		],
		'en' => [
			'title' => __( 'Homepage — English', 'mavo' ),
			'keys'  => [
				'en_trust_bar'            => __( 'Trust bar', 'mavo' ),
				'en_destinations'         => __( 'Featured destinations', 'mavo' ),
				'en_trip_type'            => __( 'Browse by trip type', 'mavo' ),
				'en_family_travel_themes' => __( 'Travel with your family', 'mavo' ),
				'en_recent_posts'         => __( 'Latest articles', 'mavo' ),
				'en_popular_last_year'    => __( 'Most read', 'mavo' ),
				'en_about_mini'           => __( 'About mini', 'mavo' ),
			],
		],
		'de' => [
			'title' => __( 'Homepage — Deutsch', 'mavo' ),
			'keys'  => [
				'de_trust_bar'            => __( 'Vertrauensleiste', 'mavo' ),
				'de_destinations'         => __( 'Beliebte Reiseziele', 'mavo' ),
				'de_trip_type'            => __( 'Nach Reiseart', 'mavo' ),
				'de_family_travel_themes' => __( 'Reisen für jede Familie', 'mavo' ),
				'de_recent_posts'         => __( 'Neueste Artikel', 'mavo' ),
				'de_popular_last_year'    => __( 'Meistgelesen', 'mavo' ),
				'de_about_mini'           => __( 'Über uns (Mini)', 'mavo' ),
			],
		],
		'sidebar' => [
			'title' => __( 'Barre latérale des résultats de recherche', 'mavo' ),
			'keys'  => [
				'sidebar_search_again'    => __( 'Nouvelle recherche (désactivé par défaut — redondant avec le formulaire en haut de page)', 'mavo' ),
				'sidebar_start_here'      => __( '« Commencez ici » (Français uniquement, géré par le modèle)', 'mavo' ),
				'sidebar_refine_theme'    => __( 'Voyagez par thème', 'mavo' ),
				'sidebar_most_read'       => __( 'Les plus lus', 'mavo' ),
				'sidebar_about'           => __( 'Mini biographie', 'mavo' ),
				'sidebar_newsletter'      => __( 'Abonnement newsletter', 'mavo' ),
				'sidebar_social'          => __( 'Réseaux sociaux', 'mavo' ),
				'sidebar_latest_articles' => __( 'Derniers articles', 'mavo' ),
			],
		],
		'footer' => [
			'title' => __( 'Pied de page (pages d’accueil + Commencez ici)', 'mavo' ),
			'keys'  => [
				'footer_newsletter' => __( 'Inclure le bloc newsletter (uniquement en mode « pied de page simplifié »)', 'mavo' ),
			],
		],
	];
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Réglages MaVo', 'mavo' ); ?></h1>

		<?php if ( isset( $_GET['mv_saved'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Réglages enregistrés.', 'mavo' ); ?></p>
			</div>
		<?php endif; ?>

		<?php $tvf_url = menu_page_url( 'travel-finder', false ); ?>
		<?php if ( $tvf_url ) : ?>
			<p>
				<?php esc_html_e( 'Réglages liés au plugin Travel Finder (filtres, thèmes familiaux, traductions) :', 'mavo' ); ?>
				<a href="<?php echo esc_url( $tvf_url ); ?>"><?php esc_html_e( 'voir cette page', 'mavo' ); ?></a>
			</p>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'mv_save_settings', 'mv_settings_nonce' ); ?>
			<input type="hidden" name="action" value="mv_save_settings">

			<h2><?php esc_html_e( 'Mode de pied de page (pages d’accueil + Commencez ici)', 'mavo' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Mode', 'mavo' ); ?></th>
					<td>
						<label style="display:block;margin-bottom:8px;">
							<input type="radio" name="footer_mode" value="widgets_2col" <?php checked( 'widgets_2col', $settings['footer_mode'] ); ?>>
							<?php esc_html_e( 'Widgets habituels, 2 colonnes (Footer Widget 1 + 3, sans le 2)', 'mavo' ); ?>
						</label>
						<label style="display:block;margin-bottom:8px;">
							<input type="radio" name="footer_mode" value="simplified" <?php checked( 'simplified', $settings['footer_mode'] ); ?>>
							<?php esc_html_e( 'Pied de page simplifié personnalisé (logo, liens, réseaux sociaux, newsletter)', 'mavo' ); ?>
						</label>
						<label style="display:block;">
							<input type="radio" name="footer_mode" value="full" <?php checked( 'full', $settings['footer_mode'] ); ?>>
							<?php esc_html_e( 'Pied de page habituel du site, sans modification (3 colonnes)', 'mavo' ); ?>
						</label>
					</td>
				</tr>
			</table>

			<?php foreach ( $groups as $group ) : ?>
				<h2><?php echo esc_html( $group['title'] ); ?></h2>
				<table class="form-table">
					<?php foreach ( $group['keys'] as $key => $label ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="sections[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $settings['sections'][ $key ] ) ); ?>>
									<?php esc_html_e( 'Afficher cette section', 'mavo' ); ?>
								</label>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>

			<h2><?php esc_html_e( 'Nombres affichés', 'mavo' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( '« Les plus lus » dans la barre latérale', 'mavo' ); ?></th>
					<td><input type="number" min="1" max="12" name="counts[sidebar_most_read_count]" value="<?php echo (int) $settings['counts']['sidebar_most_read_count']; ?>"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( '« Derniers articles » dans la barre latérale', 'mavo' ); ?></th>
					<td><input type="number" min="1" max="12" name="counts[sidebar_latest_articles_count]" value="<?php echo (int) $settings['counts']['sidebar_latest_articles_count']; ?>"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Image de remplacement', 'mavo' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'URL de l’image utilisée quand aucune photo n’existe', 'mavo' ); ?></th>
					<td><input type="url" class="regular-text" name="placeholder_image" value="<?php echo esc_attr( $settings['placeholder_image'] ); ?>"></td>
				</tr>
			</table>

			<?php submit_button( __( 'Enregistrer les réglages', 'mavo' ) ); ?>
		</form>
	</div>
	<?php
}
