<?php
/**
 * Admin metabox for per-post badge controls.
 *
 * _mv_badges_hide     — checkbox: suppress all badges on this post.
 * _mv_badges_override — JSON array: up to 3 manual badges that replace
 *                       auto-selected badges entirely (each has label + style).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', 'mv_badges_admin_add_metabox' );
function mv_badges_admin_add_metabox(): void {
	add_meta_box(
		'mv-badges-control',
		'Badges',
		'mv_badges_admin_render_metabox',
		'post',
		'side',
		'default'
	);
}

function mv_badges_admin_render_metabox( \WP_Post $post ): void {
	wp_nonce_field( 'mv_badges_save_' . $post->ID, '_mv_badges_nonce' );

	$hidden   = (bool) get_post_meta( $post->ID, '_mv_badges_hide', true );
	$raw      = get_post_meta( $post->ID, '_mv_badges_override', true );
	$overrides = [];
	if ( $raw && is_string( $raw ) ) {
		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) ) {
			$overrides = $decoded;
		}
	}

	// Pad to 3 rows for the UI.
	while ( count( $overrides ) < 3 ) {
		$overrides[] = [ 'label' => '', 'style' => '' ];
	}

	$styles = [
		''          => '— auto —',
		'primary'   => 'Primary (blue)',
		'warm'      => 'Warm (brown)',
		'highlight' => 'Highlight (pink)',
		'neutral'   => 'Neutral (grey)',
	];
	?>
	<p>
		<label style="font-weight:600;">
			<input type="checkbox" name="mv_badges_hide" value="1" <?php checked( $hidden ); ?>>
			<?php esc_html_e( 'Hide all badges on this post', 'mavo26-child' ); ?>
		</label>
	</p>
	<p style="margin:.75em 0 .25em;font-weight:600;font-size:.85em;text-transform:uppercase;letter-spacing:.04em;">
		<?php esc_html_e( 'Override badges (replaces auto)', 'mavo26-child' ); ?>
	</p>
	<p style="margin:0 0 .5em;font-size:.8em;color:#666;">
		<?php esc_html_e( 'Fill 1–3 rows to replace auto-detection. Leave all blank to use auto.', 'mavo26-child' ); ?>
	</p>
	<table style="width:100%;border-collapse:collapse;">
		<?php foreach ( $overrides as $i => $badge ) :
			$label = esc_attr( $badge['label'] ?? '' );
			$style = $badge['style'] ?? '';
		?>
		<tr>
			<td style="padding:2px 4px 2px 0;width:55%;">
				<input
					type="text"
					name="mv_badges_override[<?php echo $i; ?>][label]"
					value="<?php echo $label; ?>"
					placeholder="<?php esc_attr_e( 'Label', 'mavo26-child' ); ?>"
					style="width:100%;"
				>
			</td>
			<td style="padding:2px 0;">
				<select name="mv_badges_override[<?php echo $i; ?>][style]" style="width:100%;">
					<?php foreach ( $styles as $val => $text ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $style, $val ); ?>>
							<?php echo esc_html( $text ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

add_action( 'save_post_post', 'mv_badges_admin_save_metabox', 10, 2 );
function mv_badges_admin_save_metabox( int $post_id, \WP_Post $post ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['_mv_badges_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( $_POST['_mv_badges_nonce'] ), 'mv_badges_save_' . $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Hide flag.
	if ( ! empty( $_POST['mv_badges_hide'] ) ) {
		update_post_meta( $post_id, '_mv_badges_hide', '1' );
	} else {
		delete_post_meta( $post_id, '_mv_badges_hide' );
	}

	// Override badges.
	$allowed_styles = [ 'primary', 'warm', 'highlight', 'neutral' ];
	$badges         = [];

	$raw_rows = $_POST['mv_badges_override'] ?? [];
	if ( is_array( $raw_rows ) ) {
		foreach ( array_values( $raw_rows ) as $row ) {
			$label = wp_strip_all_tags( (string) ( $row['label'] ?? '' ) );
			$label = sanitize_text_field( $label );
			if ( '' === $label ) {
				continue;
			}
			$style    = in_array( $row['style'] ?? '', $allowed_styles, true ) ? $row['style'] : 'neutral';
			$badges[] = [ 'label' => $label, 'style' => $style ];
			if ( count( $badges ) >= 3 ) {
				break;
			}
		}
	}

	if ( ! empty( $badges ) ) {
		update_post_meta( $post_id, '_mv_badges_override', wp_json_encode( $badges ) );
	} else {
		delete_post_meta( $post_id, '_mv_badges_override' );
	}
}
