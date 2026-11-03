<?php
/**
 * Point inline post images at files that exist on this site (v27.1.4).
 * Migrated posts reference http(s)://starksocial.com/wp-content/uploads/… and
 * some sizes (e.g. -400x400) no longer exist anywhere — broken on live too.
 * For each reference: same file here → this site's URL; missing -WxH size →
 * the closest existing sibling (square 800x800 for squares, else the original).
 * Dry run unless "apply" is passed:  wp eval-file fix-inline-images.php [apply]
 */
global $wpdb;
$apply   = in_array( 'apply', (array) ( $args ?? array() ), true );
$uploads = wp_get_upload_dir();
$rows    = $wpdb->get_results( "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_type IN ('post','page','podcast') AND post_status = 'publish' AND post_content LIKE '%starksocial.com/wp-content/uploads/%'" );
$stats   = array( 'local' => 0, 'resized' => 0, 'unresolved' => 0, 'posts' => 0 );

foreach ( $rows as $row ) {
    $new = preg_replace_callback( '#(?:https?:)?//(?:www\.)?starksocial\.com/wp-content/uploads/([^"\'\s\)]+)#', function ( $m ) use ( $uploads, &$stats ) {
        $rel = $m[1];
        if ( file_exists( $uploads['basedir'] . '/' . urldecode( $rel ) ) ) { $stats['local']++; return $uploads['baseurl'] . '/' . $rel; }
        if ( preg_match( '#^(.*)-(\d+)x(\d+)(\.\w+)$#', $rel, $p ) ) {
            $candidates = $p[2] === $p[3] ? array( "{$p[1]}-800x800{$p[4]}", "{$p[1]}{$p[4]}" ) : array( "{$p[1]}{$p[4]}" );
            foreach ( $candidates as $c ) {
                if ( file_exists( $uploads['basedir'] . '/' . urldecode( $c ) ) ) { $stats['resized']++; return $uploads['baseurl'] . '/' . $c; }
            }
        }
        $stats['unresolved']++;
        WP_CLI::log( "  unresolved: $rel" );
        return $m[0];
    }, $row->post_content );
    if ( $new !== $row->post_content ) {
        $stats['posts']++;
        if ( $apply ) $wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $row->ID ) );
    }
}
if ( $apply ) wp_cache_flush();
WP_CLI::success( ( $apply ? 'Applied' : 'Dry run' ) . ': ' . wp_json_encode( $stats ) );
