<?php
$rows = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
$base = wp_get_upload_dir();
foreach ( $rows as $r ) {
    $p = $r['post']; $m = $r['meta'];
    if ( get_post( $p['ID'] ) ) { WP_CLI::log( "{$p['ID']} exists" ); continue; }
    if ( ! file_exists( $base['basedir'] . '/' . $m['_wp_attached_file'] ) ) { WP_CLI::warning( "{$p['ID']} file missing: {$m['_wp_attached_file']}" ); continue; }
    $id = wp_insert_post( array(
        'import_id' => $p['ID'], 'post_type' => 'attachment', 'post_status' => 'inherit',
        'post_title' => $p['post_title'], 'post_name' => $p['post_name'], 'post_date' => $p['post_date'],
        'post_mime_type' => $p['post_mime_type'], 'post_parent' => $p['post_parent'],
        'post_content' => $p['post_content'], 'post_excerpt' => $p['post_excerpt'],
        'guid' => $base['baseurl'] . '/' . $m['_wp_attached_file'],
    ), true );
    if ( is_wp_error( $id ) ) { WP_CLI::warning( $id->get_error_message() ); continue; }
    foreach ( $m as $k => $v ) if ( '' !== $v ) update_post_meta( $id, $k, $v );
    WP_CLI::log( "registered $id" );
}
