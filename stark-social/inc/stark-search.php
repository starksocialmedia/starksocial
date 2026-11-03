<?php
/**
 * Stark 27 search: facets, counts and highlighted snippets (v27.4.8)
 * Used by templates/stark-search.php. ?type= narrows results to one content
 * type; the counts come from one small query per type.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_search_types() {
    return array(
        'post'      => array( 'label' => 'Articles', 'type' => 'post' ),
        'portfolio' => array( 'label' => 'Case studies', 'type' => 'portfolio' ),
        'podcast'   => array( 'label' => 'Podcast', 'type' => 'podcast' ),
        'docs'      => array( 'label' => 'Knowledgebase', 'type' => 'docs' ),
        'page'      => array( 'label' => 'Pages', 'type' => 'page' ),
    );
}

function stark_search_current_type() {
    $type = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- read-only filter
    return isset( stark_search_types()[ $type ] ) ? $type : '';
}

add_action( 'pre_get_posts', function ( $q ) {
    if ( is_admin() || ! $q->is_main_query() || ! $q->is_search() ) return;
    $type = stark_search_current_type();
    $q->set( 'post_type', $type ? stark_search_types()[ $type ]['type'] : array_column( stark_search_types(), 'type' ) );
} );

/**
 * Result counts per facet for the current query: key => count (0s included).
 */
function stark_search_counts( $s ) {
    $counts = array();
    foreach ( stark_search_types() as $key => $t ) {
        $q = new WP_Query( array( 's' => $s, 'post_type' => $t['type'], 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => false ) );
        $counts[ $key ] = (int) $q->found_posts;
    }
    return $counts;
}

/**
 * Search terms from the query (2+ characters), longest first.
 */
function stark_search_terms( $s ) {
    $terms = array_filter( preg_split( '/\s+/', trim( (string) $s ) ), function ( $t ) { return mb_strlen( $t ) >= 2; } );
    usort( $terms, function ( $a, $b ) { return mb_strlen( $b ) <=> mb_strlen( $a ); } );
    return array_values( array_unique( $terms ) );
}

/**
 * About 30 words of the post around the first match, escaped, with the
 * search terms wrapped in <mark>. Falls back to the excerpt.
 */
function stark_search_snippet( $post, $s ) {
    $text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post ) ) ) ) );
    if ( '' === $text ) $text = get_the_excerpt( $post );
    $terms = stark_search_terms( $s );

    $start = 0;
    foreach ( $terms as $t ) {
        $pos = mb_stripos( $text, $t );
        if ( false !== $pos ) { $start = $pos; break; }
    }
    $from    = max( 0, $start - 70 );
    $snippet = mb_substr( $text, $from, 220 );
    if ( $from > 0 ) $snippet = '…' . preg_replace( '/^\S*\s/u', '', $snippet );
    if ( $from + 220 < mb_strlen( $text ) ) $snippet = preg_replace( '/\s\S*$/u', '', $snippet ) . '…';

    return stark_search_mark( esc_html( $snippet ), $terms );
}

/**
 * Wrap $terms in <mark> inside escaped $html, never inside an entity (&amp;).
 */
function stark_search_mark( $html, $terms ) {
    if ( ! $terms ) return $html;
    $re    = '/(' . implode( '|', array_map( function ( $t ) { return preg_quote( $t, '/' ); }, $terms ) ) . ')/iu';
    $parts = preg_split( '/(&#?[a-z0-9]+;)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
    foreach ( $parts as $i => $part ) {
        if ( '' !== $part && '&' !== $part[0] ) $parts[ $i ] = preg_replace( $re, '<mark>$1</mark>', $part );
    }
    return implode( '', $parts );
}

/**
 * The title, escaped, with search terms marked.
 */
function stark_search_title( $post, $s ) {
    return stark_search_mark( esc_html( get_the_title( $post ) ), stark_search_terms( $s ) );
}

/**
 * Suggested searches for the empty state: topics with real content behind
 * them (no analytics on popular searches yet).
 */
function stark_search_suggestions() {
    return apply_filters( 'stark_search_suggestions', array( 'SEO', 'Instagram', 'Facebook', 'website', 'passwords', 'Google Business Profile' ) );
}
