<?php
/**
 * Live fix: Brand Strategy & Identity (page 1427) shows the SEO page's process
 * steps and the Web Design page's FAQs. Replaces those two Cornerstone looper
 * data sets with the copy in STARK_JSON. Dry run unless "apply" is passed.
 *
 *   STARK_JSON=~/tmp/brand-1427.json wp --skip-themes eval-file ~/tmp/brand-1427-apply.php [apply]
 */
$page = 1427;
$key  = '_cornerstone_data';
$copy = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
if ( empty( $copy['process'] ) || empty( $copy['faq'] ) ) WP_CLI::error( 'STARK_JSON needs "process" and "faq".' );

$raw  = get_post_meta( $page, $key, true );
$data = json_decode( $raw, true );
if ( ! is_array( $data ) ) WP_CLI::error( 'Could not decode page data.' );

$found = array( 'process' => 0, 'faq' => 0 );
$walk  = function ( &$node ) use ( &$walk, &$found, $copy ) {
    if ( ! is_array( $node ) ) return;
    if ( ! empty( $node['looper_provider_json'] ) ) {
        $items = json_decode( $node['looper_provider_json'], true );
        $first = is_array( $items ) && $items ? $items[0] : array();
        // Process: the SEO steps ("Structure & optimization" is SEO-only).
        if ( isset( $first['step'] ) && in_array( 'Structure & optimization', wp_list_pluck( $items, 'title' ), true ) ) {
            $node['looper_provider_json'] = wp_json_encode( $copy['process'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
            $found['process']++;
        }
        // FAQ: the Web Design questions ("Do you write the website content?" is web-design-only).
        if ( isset( $first['q'] ) && in_array( 'Do you write the website content?', wp_list_pluck( $items, 'q' ), true ) ) {
            $node['looper_provider_json'] = wp_json_encode( $copy['faq'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
            $found['faq']++;
        }
    }
    foreach ( $node as &$child ) $walk( $child );
};
$walk( $data );

if ( 1 !== $found['process'] || 1 !== $found['faq'] ) WP_CLI::error( 'Expected one process and one FAQ looper, found ' . wp_json_encode( $found ) . '. Nothing changed.' );

$new = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
WP_CLI::log( 'Process: ' . implode( ' / ', wp_list_pluck( $copy['process'], 'title' ) ) );
WP_CLI::log( 'FAQ: ' . implode( ' / ', wp_list_pluck( $copy['faq'], 'q' ) ) );

if ( ! in_array( 'apply', (array) ( $args ?? array() ), true ) ) {
    WP_CLI::success( 'Dry run OK — pass "apply" to write. Back up first (see README).' );
    return;
}
update_post_meta( $page, $key, wp_slash( $new ) );
clean_post_cache( $page );
WP_CLI::success( 'Page 1427 updated. Purge caches: wp breeze purge --cache=all' );
