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
	$badges = mv_get_tile_badges( $post_id, $args );
	return mv_render_tile_badges( $badges, $args );
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
		'link_badges'    => false, // render badges as <a> links (only when tile is a <div>, not <a>)
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

	// link_badges only affects rendering, not badge selection — exclude from cache key.
	$cache_args = $args;
	unset( $cache_args['link_badges'] );
	$cache_key = md5( $post_id . '|' . wp_json_encode( $cache_args ) );
	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$candidates = array_merge(
		mv_get_geo_badge_candidates( $post_id, $args ),
		mv_get_finder_badge_candidates( $post_id, $args )
	);

	$candidates          = mv_filter_badge_candidates( $candidates, $args );
	$candidates          = mv_dedupe_badge_candidates( $candidates );
	$candidates          = mv_score_badge_candidates( $candidates, $args, $post_id );
	$badges              = mv_pick_badges( $candidates, $args );

	// Recolor finder badges by final priority score (geo keeps 'primary').
	foreach ( $badges as &$badge ) {
		if ( ( $badge['source'] ?? '' ) === 'finder' ) {
			$badge['style'] = _mv_finder_style_by_priority( (int) ( $badge['priority'] ?? 0 ) );
		}
	}
	unset( $badge );

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

	$link_badges = ! empty( $args['link_badges'] );
	$classes     = 'mv-tile__badges';
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
		$cls   = 'mv-badge mv-badge--' . esc_attr( $style ) . ' mv-badge--group-' . esc_attr( $group );
		$url   = ( $link_badges && ! empty( $badge['url'] ) ) ? (string) $badge['url'] : '';

		if ( $url ) {
			$html .= '<a class="' . $cls . '" href="' . esc_url( $url ) . '">'
				. esc_html( $badge['label'] )
				. '</a>';
		} else {
			$html .= '<span class="' . $cls . '">'
				. esc_html( $badge['label'] )
				. '</span>';
		}
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

	// search_result context: expose every geo level so the scorer can pick the
	// one most relevant to the query (e.g. "Marseille" should show Marseille or
	// Provence, not France). All other contexts still get a single preferred-level
	// candidate via the existing logic below.
	if ( 'search_result' === $context ) {
		$out = [];
		foreach ( [ 'city', 'region', 'country' ] as $level ) {
			if ( ! isset( $by_level[ $level ] ) ) {
				continue;
			}
			$raw_name = $by_level[ $level ]->$name_col ?? null;
			if ( ! $raw_name ) {
				continue;
			}
			$label       = mv_normalize_geo_label( $raw_name );
			$term_id_col = 'term_id_' . $lang;
			$term_id     = (int) ( $by_level[ $level ]->$term_id_col ?? 0 );
			$geo_url     = '';
			if ( $term_id ) {
				$hub_page_id = (int) get_term_meta( $term_id, '_mv_hub_page_id', true );
				if ( $hub_page_id ) {
					$geo_url = (string) get_permalink( $hub_page_id );
				} else {
					$term_link = get_term_link( $term_id, 'post_tag' );
					$geo_url   = is_wp_error( $term_link ) ? '' : (string) $term_link;
				}
			}
			$out[] = [
				'key'      => 'geo_' . $level . '_' . sanitize_title( $label ),
				'label'    => $label,
				'group'    => 'geo',
				'style'    => 'primary',
				'priority' => match ( $level ) { 'city' => 85, 'region' => 82, default => 78 },
				'source'   => 'geo',
				'value'    => $level,
				'url'      => $geo_url,
			];
		}
		return $out;
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

		$term_id_col = 'term_id_' . $lang;
		$term_id     = (int) ( $by_level[ $level ]->$term_id_col ?? 0 );
		$geo_url     = '';
		if ( $term_id ) {
			$hub_page_id = (int) get_term_meta( $term_id, '_mv_hub_page_id', true );
			if ( $hub_page_id ) {
				$geo_url = (string) get_permalink( $hub_page_id );
			} else {
				$term_link = get_term_link( $term_id, 'post_tag' );
				$geo_url   = is_wp_error( $term_link ) ? '' : (string) $term_link;
			}
		}

		return [ [
			'key'      => 'geo_' . $level . '_' . sanitize_title( $label ),
			'label'    => $label,
			'group'    => 'geo',
			'style'    => 'primary',
			'priority' => $priority,
			'source'   => 'geo',
			'value'    => $level,
			'url'      => $geo_url,
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
				'grade'    => $weight,
				'url'      => _mv_finder_url( $lang, $slug ),
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

function mv_score_badge_candidates( array $candidates, array $args = [], int $post_id = 0 ): array {
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

	// search_result: query-aware scoring.
	if ( 'search_result' === $context && apply_filters( 'mv_badges_search_context_enabled', true ) ) {
		$raw_query = trim( (string) ( $args['query'] ?? '' ) );

		if ( '' !== $raw_query ) {
			$tokens = _mv_search_tokens( $raw_query );

			// Detect which geo level the query matches among this post's candidates.
			$query_geo_type = null;
			foreach ( $candidates as $c ) {
				if ( ( $c['group'] ?? '' ) === 'geo' && _mv_candidate_in_tokens( $c, $tokens ) ) {
					$query_geo_type = $c['value'] ?? null; // country / region / city
					break;
				}
			}

			// Fall back to DB-driven geo type map when no candidate matched the query.
			if ( null === $query_geo_type ) {
				$geo_type_map = _mv_search_geo_type_map();
				foreach ( $tokens as $token ) {
					if ( isset( $geo_type_map[ $token ] ) ) {
						$query_geo_type = $geo_type_map[ $token ];
						break;
					}
				}
			}

			// Normalized post title for redundancy check (city already in title → less useful as badge).
			$title_norm = $post_id ? _mv_normalize_search_text( get_the_title( $post_id ) ) : '';

			// Pre-check: does this post have any geo candidate finer than country?
			$has_finer_geo = false;
			foreach ( $candidates as $c ) {
				if ( ( $c['group'] ?? '' ) === 'geo' && in_array( $c['value'] ?? '', [ 'city', 'region' ], true ) ) {
					$has_finer_geo = true;
					break;
				}
			}

			foreach ( $candidates as &$c ) {
				$group = $c['group'] ?? 'other';
				$level = $c['value'] ?? null;

				if ( 'geo' !== $group ) {
					if ( _mv_candidate_in_tokens( $c, $tokens ) ) {
						$c['priority'] += 40; // Query explicitly names this trip-type/age/etc.
					}
					continue;
				}

				// Geo candidates.
				$label_norm    = _mv_normalize_search_text( $c['label'] ?? '' );
				$title_has_geo = '' !== $label_norm && false !== strpos( $title_norm, $label_norm );
				$geo_matched   = _mv_candidate_in_tokens( $c, $tokens );

				if ( 'country' === $query_geo_type && 'country' === $level && $geo_matched ) {
					$c['priority'] -= 25; // Country search: country badge is redundant on every result.
				}

				if ( 'region' === $query_geo_type ) {
					if ( 'region' === $level && $geo_matched ) {
						$c['priority'] += 20;
					} elseif ( 'country' === $level ) {
						$c['priority'] -= 15;
					}
				}

				if ( 'city' === $query_geo_type ) {
					if ( 'city' === $level && $geo_matched ) {
						$c['priority'] += $title_has_geo ? -15 : 20;
					} elseif ( 'region' === $level ) {
						$c['priority'] += 15;
					} elseif ( 'country' === $level ) {
						if ( $has_finer_geo ) {
							// A city or region badge exists — country is redundant, suppress it.
							$c['suppress'] = true;
						} else {
							// Country is the only geo data; keep as fallback but downweight.
							$c['priority'] -= 30;
						}
					}
				}
			}
			unset( $c );
		}
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
		if ( ! empty( $c['suppress'] ) ) {
			continue;
		}
		if ( mv_is_forbidden_badge_label( $c['label'] ) ) {
			continue;
		}
		// Don't force a weak second badge — one strong badge is better than two weak ones.
		if ( ! empty( $selected ) && ( (int) ( $c['priority'] ?? 0 ) ) < 30 ) {
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
// Search query parsing helpers
// ---------------------------------------------------------------------------

/**
 * Normalize text for query matching: strip tags, lowercase, remove accents,
 * remove punctuation, collapse whitespace.
 */
function _mv_normalize_search_text( string $value ): string {
	$value = html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES, 'UTF-8' );
	$value = strtolower( $value );
	$value = remove_accents( $value );
	$value = preg_replace( '/[^a-z0-9\s]+/', ' ', $value );
	return trim( (string) preg_replace( '/\s+/', ' ', $value ) );
}

/**
 * Normalized query aliases: common variants → canonical normalized form that
 * matches a badge label when normalized. Keep small; do not over-engineer.
 */
function _mv_search_aliases(): array {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}
	$map = [
		'roadtrip'   => 'road trip',
		'citytrip'   => 'city trip',
		'rando'      => 'nature rando',
		'randonnee'  => 'nature rando',
		'nature'     => 'nature rando',
		'bebe'       => 'avec bebe',
		'bebes'      => 'avec bebe',
		'ado'        => 'avec ados',
		'london'     => 'londres',
		'londre'     => 'londres',
		'marseilles' => 'marseille',
		'cote azur'  => 'provence',
		'paca'       => 'provence',
		'uk'         => 'royaume-uni',
		// 'angleterre' deliberately NOT aliased to 'royaume-uni' —
		// the geo hierarchy distinguishes England from UK.
	];
	return $map;
}

/**
 * Build a map of normalized place name → geo level (country/region/city) from
 * the geo_tagger_places table. Cached as a transient for 24 h so search scoring
 * can detect query geo type even when a post isn't tagged at that level.
 */
function _mv_search_geo_type_map(): array {
	static $local = null;
	if ( null !== $local ) {
		return $local;
	}

	$cached = get_transient( '_mv_geo_type_map' );
	if ( false !== $cached && is_array( $cached ) ) {
		$local = $cached;
		return $local;
	}

	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$rows = $wpdb->get_results(
		"SELECT name_fr, name_en, name_de, level FROM {$wpdb->prefix}geo_tagger_places WHERE level IN ('country','region','city')"
	);

	$local = [];
	foreach ( (array) $rows as $row ) {
		$level = $row->level ?? '';
		foreach ( [ $row->name_fr ?? '', $row->name_en ?? '', $row->name_de ?? '' ] as $raw ) {
			if ( '' === $raw ) {
				continue;
			}
			$key = _mv_normalize_search_text( $raw );
			if ( '' !== $key && ! isset( $local[ $key ] ) ) {
				$local[ $key ] = $level;
			}
			$display = mv_normalize_geo_label( $raw );
			if ( $display !== $raw ) {
				$key2 = _mv_normalize_search_text( $display );
				if ( '' !== $key2 && ! isset( $local[ $key2 ] ) ) {
					$local[ $key2 ] = $level;
				}
			}
		}
	}

	set_transient( '_mv_geo_type_map', $local, DAY_IN_SECONDS );
	return $local;
}

/**
 * Parse a raw search query into a set of normalized tokens (unigrams + bigrams)
 * with aliases applied. Cached statically — parsed once per request per query.
 */
function _mv_search_tokens( string $raw ): array {
	static $cache = [];
	if ( isset( $cache[ $raw ] ) ) {
		return $cache[ $raw ];
	}

	$normalized = _mv_normalize_search_text( $raw );
	$aliases    = _mv_search_aliases();
	$stopwords  = [ 'a', 'au', 'aux', 'de', 'des', 'du', 'en', 'et', 'la', 'le', 'les', 'pour', 'avec', 'un', 'une', 'sur', 'par' ];

	$words  = preg_split( '/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY );
	$tokens = [];

	foreach ( $words as $w ) {
		if ( ! in_array( $w, $stopwords, true ) ) {
			$tokens[] = $w;
		}
		if ( isset( $aliases[ $w ] ) ) {
			$tokens[] = $aliases[ $w ];
		}
	}

	for ( $i = 0, $n = count( $words ); $i < $n - 1; $i++ ) {
		$bg       = $words[ $i ] . ' ' . $words[ $i + 1 ];
		$tokens[] = $bg;
		if ( isset( $aliases[ $bg ] ) ) {
			$tokens[] = $aliases[ $bg ];
		}
	}

	$cache[ $raw ] = array_values( array_unique( $tokens ) );
	return $cache[ $raw ];
}

/**
 * Whether a candidate's normalized label appears in the token set.
 */
function _mv_candidate_in_tokens( array $c, array $tokens ): bool {
	$label = _mv_normalize_search_text( $c['label'] ?? '' );
	return '' !== $label && in_array( $label, $tokens, true );
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
 * Map a finder badge's final priority score to a visual style.
 * Geo badges always use 'primary' and never pass through here.
 *
 * Thresholds to tune:
 *   ≥ 60  → highlight  (strong signal — query match or high-weight tag)
 *   ≥ 40  → warm       (normal relevance)
 *   < 40  → neutral    (weak signal, close to the show-cutoff of 30)
 */
function _mv_finder_style_by_priority( int $priority ): string {
	if ( $priority >= 60 ) {
		return 'highlight';
	}
	if ( $priority >= 40 ) {
		return 'warm';
	}
	return 'neutral';
}

/**
 * Language-aware finder URL for a TVF filter slug.
 * FR/EN/DE finder page slugs must match Polylang's translated page slugs exactly.
 */
function _mv_finder_url( string $lang, string $slug ): string {
	static $bases = [
		'fr' => 'https://www.mamanvoyage.com/nos-idees-de-voyage/',
		'en' => 'https://www.mamanvoyage.com/en/our-travel-ideas/',
		'de' => 'https://www.mamanvoyage.com/de/unsere-reiseideen/',
	];
	$base = $bases[ $lang ] ?? $bases['fr'];
	return $base . '?f=' . rawurlencode( $slug );
}

/**
 * Resolve the post's language (Polylang-aware, falls back to 'fr').
 */
function _mv_badge_lang( int $post_id ): string {
	$lang = function_exists( 'pll_get_post_language' )
		? (string) pll_get_post_language( $post_id )
		: 'fr';
	return in_array( $lang, [ 'fr', 'en', 'de' ], true ) ? $lang : 'fr';
}

/**
 * Build the current_geo context array from a WP_Term (for geo hub archive pages).
 * Returns ['type' => 'country', 'slug' => 'france'] or null if not resolvable.
 */
function mv_current_geo_from_term( \WP_Term $term ): ?array {
	if ( ! class_exists( '\GeoTagger\PlaceRepository' ) ) {
		return null;
	}
	static $repo = null;
	if ( null === $repo ) {
		$repo = new \GeoTagger\PlaceRepository();
	}
	$place = $repo->get_place_by_term_id( $term->term_id );
	if ( ! $place ) {
		return null;
	}
	$level = $place->level ?? null;
	if ( ! in_array( $level, [ 'country', 'region', 'city' ], true ) ) {
		return null;
	}
	$name  = (string) ( $place->name_fr ?? $place->name_en ?? '' );
	$label = mv_normalize_geo_label( $name );
	return [
		'type' => $level,
		'slug' => sanitize_title( $label ),
	];
}

/**
 * Returns the current_geo context for the queried page when that page is
 * configured as a geo hub (has _mv_geo_term_id post meta pointing to a tag).
 * Used by tile shortcodes so badges suppress the hub's own level automatically.
 */
function mv_page_current_geo(): ?array {
	static $cache = [];

	$page_id = (int) get_queried_object_id();
	if ( ! $page_id ) {
		return null;
	}

	if ( array_key_exists( $page_id, $cache ) ) {
		return $cache[ $page_id ];
	}

	$term_id = (int) get_post_meta( $page_id, '_mv_geo_term_id', true );
	if ( ! $term_id ) {
		$cache[ $page_id ] = null;
		return null;
	}

	if ( ! class_exists( '\GeoTagger\PlaceRepository' ) ) {
		$cache[ $page_id ] = null;
		return null;
	}

	static $repo = null;
	if ( null === $repo ) {
		$repo = new \GeoTagger\PlaceRepository();
	}

	$place = $repo->get_place_by_term_id( $term_id );
	if ( ! $place ) {
		$cache[ $page_id ] = null;
		return null;
	}

	$level = $place->level ?? null;
	if ( ! in_array( $level, [ 'country', 'region', 'city' ], true ) ) {
		$cache[ $page_id ] = null;
		return null;
	}

	$name              = (string) ( $place->name_fr ?? $place->name_en ?? '' );
	$label             = mv_normalize_geo_label( $name );
	$result            = [ 'type' => $level, 'slug' => sanitize_title( $label ) ];
	$cache[ $page_id ] = $result;
	return $result;
}
