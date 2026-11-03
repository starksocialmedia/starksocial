<?php
/**
 * Register WebP renders of SVG episode covers (v27.1.4). For every SVG
 * attachment with sibling "{name}-1280x720.webp" / "{name}-640x360.webp" files,
 * stores them in `_stark_webp_sizes` so stark_attachment_img() serves WebP.
 * Files are made by svg2webp.mjs (README). Idempotent.
 *   wp eval-file register-webp-covers.php
 */
global $wpdb;
$ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE '%.svg'" );
$n = 0;
foreach ( $ids as $id ) {
    $svg   = get_attached_file( $id );
    $sizes = array();
    foreach ( array( array( 1280, 720 ), array( 640, 360 ) ) as $wh ) {
        $file = preg_replace( '/\.svg$/i', "-{$wh[0]}x{$wh[1]}.webp", $svg );
        if ( file_exists( $file ) ) $sizes[] = array( 'file' => basename( $file ), 'width' => $wh[0], 'height' => $wh[1] );
    }
    if ( $sizes ) { update_post_meta( $id, '_stark_webp_sizes', $sizes ); $n++; }
}
WP_CLI::success( "$n SVG attachments have WebP renders" );
