<?php
/**
 * Maman Voyage badge system.
 * Shared resolver + renderer called by tiles in the child theme and mavo-* plugins.
 *
 * Plugins must guard calls: if ( function_exists( 'mv_tile_badges' ) ) { ... }
 * Global kill switch: define( 'MV_BADGES_DISABLED', true ) or add_filter( 'mv_badges_disabled', '__return_true' ).
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Public API
// ---------------------------------------------------------------------------

/**
 * Resolve and render badges in one call (convenience wrapper).
 */
function mv_tile_badges( int $post_id, array $args = [] ): string {
	return mv_render_tile_badges( mv_get_tile_badges( $post_id, $args ), $args );
}

/**
 * Resolve display badges for a post/tile in a specific context.
 *
 * @param int   $post_id
 * @param array $args {
 *   @type string $context        default|homepage_media|search_result|finder_result|geo_hub|article_related|overlay|directory
 *   @type int    $limit          Max badges shown. Default 2.
 *   @type array  $current_geo    Hub geo context: ['type' => 'country', 'slug' => 'france']
 *   @type array  $active_filters Active finder filter slugs.
 *   @type string $query          Search query string.
 *   @type array  $allow_groups   Allowed badge groups.
 * }
 * @return array[] Badge arrays with keys: key, label, group, style, priority, source.
 */
function mv_get_tile_badges( int $post_id, array $args = [] ): array {
	static $cache = [];

	$defaults = [
		'context'        => 'default',
		'limit'          => 2,
		'current_geo'    => null,
		'active_filters' => [],
		'query'          => '',
		'allow_groups'   => [ 'geo', 'trip_type', 'age', 'season', 'duration', 'budget', 'editorial' ],
	];

	$args    = wp_parse_args( $args, $defaults );
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return [];
	}

	if ( apply_filters( 'mv_badges_disabled', false ) ) {
		return [];
	}

	if ( defined( 'MV_BADGES_DISABLED' ) && MV_BADGES_DISABLED ) {
		return [];
	}

	if ( mv_badges_are_hidden( $post_id ) ) {
		return [];
	}

	$manual = mv_get_manual_badge_override( $post_id );
	if ( ! empty( $manual ) ) {
		return array_slice( $manual, 0, absint( $args['limit'] ) );
	}

	$cache_key = md5( $post_id . '|' . wp_json_encode( $args ) );
	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$candidates = array_merge(
		mv_get_geo_badge_candidates( $post_id, $args ),
		mv_get_finder_badge_candidates( $post_id, $args )
	);

	$candidates          = mv_filter_badge_candidates( $candidates, $args );
	$candidates          = mv_dedupe_badge_candidates( $candidates );
	$candidates          = mv_score_badge_candidates( $candidates, $args );
	$badges              = mv_pick_badges( $candidates, $args );
	$cache[ $cache_key ] = $badges;

	return $badges;
}

/**
 * Render badge markup from a resolved badges array.
 */
function mv_render_tile_badges( array $badges, array $args = [] ): string {
	if ( empty( $badges ) ) {
		return '';
	}

	$classes = 'mv-tile__badges';
	if ( ! empty( $args['class'] ) ) {
		$classes .= ' ' . sanitize_html_class( $args['class'] );
	}

	$html = '<div class="' . esc_attr( $classes ) . '" aria-label="' . esc_attr__( 'Repères', 'mavo26-child' ) . '">';

	foreach ( $badges as $badge ) {
		if ( empty( $badge['label'] ) ) {
			continue;
		}
		$style = ! empty( $badge['style'] ) ? sanitize_html_class( $badge['style'] ) : 'neutral';
		$group = ! empty( $badge['group'] ) ? sanitize_html_class( $badge['group'] ) : 'other';
		$html .= '<span class="mv-badge mv-badge--' . esc_attr( $style ) . ' mv-badge--group-' . esc_attr( $group ) . '">'
			. esc_html( $badge['label'] )
			. '</span>';
	}

	$html .= '</div>';
	return $html;
}

// ---------------------------------------------------------------------------
// Candidate collectors
// ---------------------------------------------------------------------------

/**
 * Collect geo badge candidates from mavo-geotag-plus PlaceRepository.
 * Uses a static chain cache (per post+lang) to avoid redundant DB queries
 * when the same post appears multiple times in a page loop.
 */
function mv_get_geo_badge_candidates( int $post_id, array $args = [] ): array {
	static $repo   = null;
	static $chains = [];

	if ( ! class_exists( '\GeoTagger\PlaceRepository' ) ) {
		return [];
	}

	$lang      = _mv_badge_lang( $post_id );
	$chain_key = $post_id . '_' . $lang;

	if ( ! array_key_exists( $chain_key, $chains ) ) {
		if ( null === $repo ) {
			$repo = new \GeoTagger\PlaceRepository();
		}
		$chains[ $chain_key ] = $repo->get_chain_for_post( $post_id, $lang );
	}

	$chain = $chains[ $chain_key ];
	if ( empty( $chain ) ) {
		return [];
	}

	$name_col    = 'name_' . $lang;
	$context     = $args['context'] ?? 'default';
	$current_geo = $args['current_geo'] ?? null;

	// Build level → place map; skip world/continent.
	$by_level = [];
	foreach ( $chain as $place ) {
		if ( in_array( $place->level, [ 'world', 'continent' ], true ) ) {
			continue;
		}
		$by_level[ $place->level ] = $place;
	}

	if ( empty( $by_level ) ) {
		return [];
	}

	// Determine preferred geo level.
	if ( $context === 'overlay' ) {
		$preferred = 'country';
	} elseif ( $current_geo ) {
		$preferred = match ( $current_geo['type'] ?? '' ) {
			'country' => 'region',
			'region'  => 'city',
			default   => 'country',
		};
	} else {
		$preferred = 'country';
	}

	$levels_to_try = match ( $preferred ) {
		'city'   => [ 'city', 'region', 'country' ],
		'region' => [ 'region', 'country', 'city' ],
		default  => [ 'country', 'region', 'city' ],
	};

	foreach ( $levels_to_try as $level ) {
		if ( ! isset( $by_level[ $level ] ) ) {
			continue;
		}

		$raw_name = $by_level[ $level ]->$name_col ?? null;
		if ( ! $raw_name ) {
			continue;
		}

		$label = mv_normalize_geo_label( $raw_name );

		// Skip if label matches the current hub.
		if ( $current_geo ) {
			$hub_slug = $current_geo['slug'] ?? '';
			if ( $hub_slug && sanitize_title( $label ) === $hub_slug ) {
				continue;
			}
		}

		$priority = match ( $level ) {
			'city'   => 85,
			'region' => 82,
			default  => 78,
		};
		if ( $level === $preferred ) {
			$priority += 5;
		}

		return [ [
			'key'      => 'geo_' . $level . '_' . sanitize_title( $label ),
			'label'    => $label,
			'group'    => 'geo',
			'style'    => 'primary',
			'priority' => $priority,
			'source'   => 'geo',
			'value'    => $level,
		] ];
	}

	return [];
}

/**
 * Collect finder-weight badge candidates from TVF_Store.
 */
function mv_get_finder_badge_candidates( int $post_id, array $args = [] ): array {
	if ( ! class_exists( 'TVF_Store' ) ) {
		return [];
	}

	$lang    = _mv_badge_lang( $post_id );
	$weights = TVF_Store::get_weights( $post_id, $lang );
	if ( empty( $weights ) ) {
		return [];
	}

	$map            = mv_get_badge_value_map();
	$context        = $args['context'] ?? 'default';
	$active_filters = $args['active_filters'] ?? [];
	$candidates     = [];

	foreach ( $map as $group => $entries ) {
		foreach ( $entries as $slug => $config ) {
			$weight = (int) ( $weights[ $slug ] ?? 0 );
			if ( $weight < 1 ) {
				continue; // Grade 0 = never badge
			}

			$priority = (int) $config['priority'];
			if ( 1 === $weight ) {
				$priority -= 15; // Grade 1 = fallback only
			}

			// Boost slugs matching active finder filters in finder_result context.
			if ( 'finder_result' === $context && in_array( $slug, $active_filters, true ) ) {
				$priority += 30;
			}

			$candidates[] = [
				'key'      => $group . '_' . $slug,
				'label'    => $config['label'],
				'group'    => $group,
				'style'    => $config['style'],
				'priority' => $priority,
				'source'   => 'finder',
				'value'    => $slug,
			];
		}
	}

	return $candidates;
}

// ---------------------------------------------------------------------------
// Pipeline: filter → dedupe → score → pick
// ---------------------------------------------------------------------------

function mv_filter_badge_candidates( array $candidates, array $args = [] ): array {
	$forbidden    = mv_forbidden_badge_labels();
	$allow_groups = $args['allow_groups'] ?? [ 'geo', 'trip_type', 'age', 'season', 'duration', 'budget', 'editorial' ];

	return array_values( array_filter( $candidates, static function ( $c ) use ( $forbidden, $allow_groups ) {
		if ( empty( $c['label'] ) ) {
			return false;
		}
		if ( in_array( $c['label'], $forbidden, true ) ) {
			return false;
		}
		return in_array( $c['group'] ?? 'other', $allow_groups, true );
	} ) );
}

function mv_dedupe_badge_candidates( array $candidates ): array {
	$seen_keys   = [];
	$seen_labels = [];
	$out         = [];

	foreach ( $candidates as $c ) {
		$key   = $c['key'] ?? '';
		$label = mb_strtolower( $c['label'] ?? '' );

		if ( $key && isset( $seen_keys[ $key ] ) ) {
			continue;
		}
		if ( $label && isset( $seen_labels[ $label ] ) ) {
			continue;
		}

		if ( $key )   { $seen_keys[ $key ]     = true; }
		if ( $label ) { $seen_labels[ $label ] = true; }
		$out[] = $c;
	}

	return $out;
}

function mv_score_badge_candidates( array $candidates, array $args = [] ): array {
	$context = $args['context'] ?? 'default';

	// Overlay tiles: geo only, one badge.
	if ( 'overlay' === $context ) {
		return array_values( array_filter( $candidates, static fn( $c ) => ( $c['group'] ?? '' ) === 'geo' ) );
	}

	// article_related: suppress low-signal practical badges.
	if ( 'article_related' === $context ) {
		foreach ( $candidates as &$c ) {
			if ( in_array( $c['group'] ?? '', [ 'season', 'duration', 'budget' ], true ) ) {
				$c['priority'] = max( 0, ( (int) ( $c['priority'] ?? 0 ) ) - 25 );
			}
		}
		unset( $c );
	}

	return array_values( $candidates );
}

function mv_pick_badges( array $candidates, array $args = [] ): array {
	$limit = max( 0, absint( $args['limit'] ?? 2 ) );
	if ( $limit < 1 || empty( $candidates ) ) {
		return [];
	}

	usort( $candidates, static function ( $a, $b ) {
		return ( (int) ( $b['priority'] ?? 0 ) ) <=> ( (int) ( $a['priority'] ?? 0 ) );
	} );

	$selected    = [];
	$used_groups = [];

	foreach ( $candidates as $c ) {
		if ( count( $selected ) >= $limit ) {
			break;
		}
		if ( empty( $c['label'] ) ) {
			continue;
		}
		$group = $c['group'] ?? 'other';
		if ( isset( $used_groups[ $group ] ) ) {
			continue; // No two badges from the same group
		}
		if ( mv_is_forbidden_badge_label( $c['label'] ) ) {
			continue;
		}
		$selected[]            = $c;
		$used_groups[ $group ] = true;
	}

	return $selected;
}

// ---------------------------------------------------------------------------
// Manual override / hide
// ---------------------------------------------------------------------------

function mv_badges_are_hidden( int $post_id ): bool {
	return (bool) get_post_meta( $post_id, '_mv_badges_hide', true );
}

function mv_get_manual_badge_override( int $post_id ): array {
	$raw = get_post_meta( $post_id, '_mv_badges_override', true );
	if ( ! $raw || ! is_string( $raw ) ) {
		return [];
	}

	$decoded = json_decode( $raw, true );
	if ( ! is_array( $decoded ) ) {
		return [];
	}

	$allowed = [ 'primary', 'warm', 'highlight', 'neutral' ];
	$badges  = [];

	foreach ( $decoded as $item ) {
		if ( empty( $item['label'] ) ) {
			continue;
		}
		$label = wp_strip_all_tags( (string) $item['label'] );
		if ( '' === $label ) {
			continue;
		}
		$style    = in_array( $item['style'] ?? '', $allowed, true ) ? $item['style'] : 'neutral';
		$badges[] = [
			'key'      => 'manual_' . sanitize_title( $label ),
			'label'    => $label,
			'group'    => 'editorial',
			'style'    => $style,
			'priority' => 100,
			'source'   => 'manual',
		];
		if ( count( $badges ) >= 3 ) {
			break;
		}
	}

	return $badges;
}

// ---------------------------------------------------------------------------
// Data tables
// ---------------------------------------------------------------------------

/**
 * Map of badgeable finder filter slugs.
 * Slugs must match wp_tvf_post_filter.filter_slug values exactly.
 * Geo filters (france, angleterre, etc.) are intentionally omitted — geo
 * badges come from the PlaceRepository, not the finder table.
 */
function mv_get_badge_value_map(): array {
	return [
		'trip_type' => [
			'roadtrip'          => [ 'label' => 'Road trip',         'style' => 'warm',      'priority' => 70 ],
			'nature_rando'      => [ 'label' => 'Nature & rando',    'style' => 'warm',      'priority' => 65 ],
			'plage_cote'        => [ 'label' => 'Plage',             'style' => 'warm',      'priority' => 62 ],
			'culture_histoire'  => [ 'label' => 'Culture',           'style' => 'warm',      'priority' => 60 ],
			'gastronomie'       => [ 'label' => 'Gastronomie',       'style' => 'warm',      'priority' => 58 ],
			'activites_famille' => [ 'label' => 'Activités famille', 'style' => 'highlight', 'priority' => 58 ],
			'detente'           => [ 'label' => 'Détente',           'style' => 'warm',      'priority' => 56 ],
			'velo'              => [ 'label' => 'Vélo',              'style' => 'warm',      'priority' => 54 ],
			'voile'             => [ 'label' => 'Voile',             'style' => 'warm',      'priority' => 54 ],
			'shopping'          => [ 'label' => 'Shopping',          'style' => 'neutral',   'priority' => 50 ],
		],
		'age' => [
			'bebes' => [ 'label' => 'Avec bébé', 'style' => 'highlight', 'priority' => 62 ],
			'ados'  => [ 'label' => 'Avec ados', 'style' => 'highlight', 'priority' => 62 ],
			// 'kids' intentionally omitted — "Avec enfants" is on the denylist
		],
		'duration' => [
			'2_3_jours' => [ 'label' => '2–4 jours',        'style' => 'neutral', 'priority' => 48 ],
			'semaine'   => [ 'label' => '1 semaine',         'style' => 'neutral', 'priority' => 48 ],
			'plus'      => [ 'label' => "Plus d'1 semaine", 'style' => 'neutral', 'priority' => 44 ],
		],
		'budget' => [
			'economique' => [ 'label' => 'Petit budget', 'style' => 'neutral', 'priority' => 52 ],
		],
		'season' => [
			'hiver'     => [ 'label' => 'Hiver',     'style' => 'neutral', 'priority' => 46 ],
			'ete'       => [ 'label' => 'Été',        'style' => 'neutral', 'priority' => 46 ],
			'printemps' => [ 'label' => 'Printemps',  'style' => 'neutral', 'priority' => 44 ],
			'automne'   => [ 'label' => 'Automne',    'style' => 'neutral', 'priority' => 44 ],
		],
	];
}

function mv_forbidden_badge_labels(): array {
	return [
		'Voyage', 'Voyages', 'Famille', 'En famille',
		'Avec enfants', 'Nos voyages', 'Europe', 'Blog',
		'Carnet de voyage', 'Article', 'Conseils',
	];
}

function mv_is_forbidden_badge_label( string $label ): bool {
	return in_array( $label, mv_forbidden_badge_labels(), true );
}

/**
 * Normalize verbose administrative region names to friendly display labels.
 */
function mv_normalize_geo_label( string $name ): string {
	static $map = [
		"Provence-Alpes-Côte d'Azur" => 'Provence',
		'Auvergne-Rhône-Alpes'       => 'Alpes',
		'Bourgogne-Franche-Comté'    => 'Bourgogne',
		'Nouvelle-Aquitaine'         => 'Aquitaine',
		'Hauts-de-France'            => 'Nord',
		'Centre-Val de Loire'        => 'Val de Loire',
	];
	return $map[ $name ] ?? $name;
}

// ---------------------------------------------------------------------------
// Internal helpers
// ---------------------------------------------------------------------------

/**
 * Resolve the post's language (Polylang-aware, falls back to 'fr').
 */
function _mv_badge_lang( int $post_id ): string {
	$lang = function_exists( 'pll_get_post_language' )
		? (string) pll_get_post_language( $post_id )
		: 'fr';
	return in_array( $lang, [ 'fr', 'en', 'de' ], true ) ? $lang : 'fr';
}
