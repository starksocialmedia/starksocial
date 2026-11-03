<?php
// After originals are downscaled on disk (cap_originals.py), store their real width/height
// in attachment metadata so srcset/`full` dimensions are correct. Idempotent.
global $wpdb;
$n = 0;
foreach ( $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file'" ) as $row ) {
    $file = get_attached_file( $row->post_id );
    if ( ! $file || ! file_exists( $file ) || preg_match( '/\.(svg|gif)$/i', $file ) ) continue;
    $meta = wp_get_attachment_metadata( $row->post_id );
    if ( ! is_array( $meta ) || empty( $meta['width'] ) ) continue;
    $size = @getimagesize( $file );
    if ( ! $size || ( (int) $meta['width'] === $size[0] && (int) $meta['height'] === $size[1] ) ) continue;
    $meta['width'] = $size[0]; $meta['height'] = $size[1]; $meta['filesize'] = filesize( $file );
    wp_update_attachment_metadata( $row->post_id, $meta );
    $n++;
}
WP_CLI::success( "$n attachments updated" );
