<?php
// Site icon = live's icon (2026/01/cropped-stark-social-media-agency-icon.webp, live attachment 1590).
// WordPress then prints favicon / apple-touch-icon tags and serves /favicon.ico. (v27.3.14)
$file = '2026/01/cropped-stark-social-media-agency-icon.webp';
$path = wp_get_upload_dir()['basedir'] . '/' . $file;
if ( ! file_exists( $path ) ) WP_CLI::error( "$file missing — sync uploads first." );
$id = (int) attachment_url_to_postid( wp_get_upload_dir()['baseurl'] . '/' . $file );
if ( ! $id ) {
    $id = wp_insert_attachment( array( 'post_title' => 'Stark Social Media Agency', 'post_mime_type' => 'image/webp', 'post_status' => 'inherit' ), $file );
}
$sizes = array();
foreach ( array( 32, 180, 192, 270, 360 ) as $px ) {
    $sizes[ "site_icon-$px" ] = array( 'file' => "cropped-stark-social-media-agency-icon-{$px}x{$px}.webp", 'width' => $px, 'height' => $px, 'mime-type' => 'image/webp' );
}
wp_update_attachment_metadata( $id, array( 'width' => 512, 'height' => 512, 'file' => $file, 'sizes' => $sizes ) );
update_post_meta( $id, '_wp_attachment_context', 'site-icon' );
update_option( 'site_icon', $id );
WP_CLI::success( "Site icon $id" );
