<?php
/**
 * Admin UI for linking a WordPress tag to a dedicated geo hub landing page.
 *
 * Stores two pieces of meta:
 *   _mv_hub_page_id  (term meta)  — page ID of the landing page
 *   _mv_geo_term_id  (post meta)  — term ID of the associated tag (reverse link)
 *
 * Setting a hub page means:
 *   - Geo badge links for that tag point to the landing page instead of /tag/slug/
 *   - Tile shortcodes on the landing page suppress that level's badge automatically
 */

defined( 'ABSPATH' ) || exit;

// -------------------------------------------------------------------------
// Tag edit screen — add / show the hub page field
// -------------------------------------------------------------------------

add_action( 'post_tag_edit_form_fields', 'mv_geo_hub_edit_form_fields', 10, 1 );
function mv_geo_hub_edit_form_fields( \WP_Term $term ): void {
	$hub_page_id = (int) get_term_meta( $term->term_id, '_mv_hub_page_id', true );
	$hub_url     = $hub_page_id ? get_permalink( $hub_page_id ) : '';
	wp_nonce_field( 'mv_geo_hub_save_' . $term->term_id, '_mv_geo_hub_nonce' );
	?>
	<tr class="form-field">
		<th scope="row">
			<label for="mv-geo-hub-url"><?php esc_html_e( 'Hub landing page', 'mavo26-child' ); ?></label>
		</th>
		<td>
			<input
				type="text"
				name="mv_geo_hub_url"
				id="mv-geo-hub-url"
				value="<?php echo esc_attr( $hub_url ); ?>"
				class="large-text"
				placeholder="<?php esc_attr_e( 'e.g. /france/', 'mavo26-child' ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'URL of the dedicated landing page for this geo tag. Geo badge links will point here instead of the default tag archive. Leave blank to use the tag archive.', 'mavo26-child' ); ?>
			</p>
			<?php if ( $hub_page_id ) : ?>
				<p class="description" style="margin-top:.4em;">
					<?php
					/* translators: %d: page ID */
					printf( esc_html__( 'Currently linked to page ID %d.', 'mavo26-child' ), $hub_page_id );
					?>
				</p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

// -------------------------------------------------------------------------
// Save
// -------------------------------------------------------------------------

add_action( 'edited_post_tag', 'mv_geo_hub_save_tag_fields', 10, 1 );
function mv_geo_hub_save_tag_fields( int $term_id ): void {
	if ( ! isset( $_POST['_mv_geo_hub_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( $_POST['_mv_geo_hub_nonce'] ), 'mv_geo_hub_save_' . $term_id ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	// Remove the old reverse link from the previously linked page (if any).
	$old_page_id = (int) get_term_meta( $term_id, '_mv_hub_page_id', true );
	if ( $old_page_id ) {
		delete_post_meta( $old_page_id, '_mv_geo_term_id' );
	}

	$raw_url = isset( $_POST['mv_geo_hub_url'] ) ? sanitize_text_field( wp_unslash( $_POST['mv_geo_hub_url'] ) ) : '';

	if ( '' === $raw_url ) {
		delete_term_meta( $term_id, '_mv_hub_page_id' );
		return;
	}

	// Accept relative paths (/france/) by prepending home_url.
	if ( str_starts_with( $raw_url, '/' ) ) {
		$raw_url = home_url( $raw_url );
	}

	$page_id = url_to_postid( $raw_url );
	if ( ! $page_id ) {
		// URL doesn't resolve to a post — save nothing (keep old value cleared).
		delete_term_meta( $term_id, '_mv_hub_page_id' );
		return;
	}

	update_term_meta( $term_id, '_mv_hub_page_id', $page_id );
	update_post_meta( $page_id, '_mv_geo_term_id', $term_id );
}
