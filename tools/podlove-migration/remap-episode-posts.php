<?php
// Remap wp_podlove_episode.post_id from live post IDs to this site's podcast post IDs, by slug.
global $wpdb;
$map = array();
foreach ( file( getenv( 'STARK_POSTS' ), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
    list( $live_id, $slug ) = explode( "\t", $line );
    $local = get_page_by_path( $slug, OBJECT, 'podcast' );
    $map[ (int) $live_id ] = $local ? $local->ID : 0;
}
$t = $wpdb->prefix . 'podlove_episode';
$done = $missing = 0;
// Two passes via a temporary negative ID so a remap can't collide with an unprocessed row.
foreach ( $wpdb->get_results( "SELECT id, post_id FROM $t" ) as $row ) {
    $new = $map[ (int) $row->post_id ] ?? 0;
    if ( $new ) { $wpdb->update( $t, array( 'post_id' => -$new ), array( 'id' => $row->id ) ); $done++; }
    else { $missing++; WP_CLI::log( "  no local post for live post {$row->post_id} (episode row {$row->id})" ); }
}
$wpdb->query( "UPDATE $t SET post_id = -post_id WHERE post_id < 0" );
WP_CLI::success( "$done episodes remapped, $missing without a local post" );
