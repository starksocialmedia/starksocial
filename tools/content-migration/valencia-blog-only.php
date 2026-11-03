<?php
/**
 * v27.5.1: Valencia Jazz & Blues is a blog post (the 2017 Communicator Award), not a case study.
 * - Moves the Phase 2-only portfolio entry to the trash (reversible; /portfolio/valencia-jazz-blues/
 *   then 301s to the post via inc/stark-redirects.php)
 * - Restores the three website screenshots the post had on live (a Cornerstone slider, attachments
 *   525–527) that the Cornerstone strip in tools/blog-snapshot/ dropped. The files were already in
 *   uploads; this registers them as attachments and puts them back where the slider was
 * Idempotent. Usage: wp eval-file valencia-blog-only.php
 */

$case = get_page_by_path( 'valencia-jazz-blues', OBJECT, 'portfolio' );
if ( $case && 'trash' !== $case->post_status ) {
    wp_trash_post( $case->ID );
    WP_CLI::log( "portfolio {$case->ID} → trash" );
}

$post = get_page_by_path( 'stark-social-wins-excellence-in-web-design-award-for-valencia-jazz-blues', OBJECT, 'post' );
if ( ! $post ) WP_CLI::error( 'award post not found' );
if ( false !== strpos( $post->post_content, 'stark-award-shots' ) ) { WP_CLI::success( 'screenshots already restored' ); return; }

require_once ABSPATH . 'wp-admin/includes/image.php';
$uploads = wp_get_upload_dir();
// Live order (slider 525, 527, 526) and live filenames ("valenca" is live's spelling).
$shots = array(
    '2024/07/valenca-jazz-and-blues-santa-clarita-website-design.webp' => 'Valencia Jazz & Blues website on a laptop',
    '2024/07/valenca-jazz-and-blues-website-design-macbook-ipad.webp'  => 'Valencia Jazz & Blues website on a MacBook and iPad',
    '2024/07/valenca-jazz-and-blues-website-design-iphone.webp'        => 'Valencia Jazz & Blues website on an iPhone',
);
$figs = array();
foreach ( $shots as $rel => $alt ) {
    $file = $uploads['basedir'] . '/' . $rel;
    if ( ! is_file( $file ) ) WP_CLI::error( "missing $rel" );
    global $wpdb;
    $id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s", $rel ) );
    if ( ! $id ) {
        $id = wp_insert_attachment( array( 'post_mime_type' => 'image/webp', 'post_title' => $alt, 'post_status' => 'inherit' ), $file, $post->ID );
        wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
        WP_CLI::log( "attachment $id ← $rel" );
    }
    update_post_meta( $id, '_wp_attachment_image_alt', $alt );
    $img    = wp_get_attachment_image_src( $id, 'large' );
    // 3-up at ~230px on desktop, full width on phones (v27.5.10: without sizes, browsers took the 1024px file)
    $figs[] = sprintf( '<figure class="wp-block-image size-large"><img src="%s" alt="%s" width="%d" height="%d" class="wp-image-%d" sizes="(max-width: 600px) calc(100vw - 32px), 240px" loading="lazy"></figure>', esc_url( $img[0] ), esc_attr( $alt ), $img[1], $img[2], $id );
}
$gallery = "\n<div class=\"stark-award-shots\">\n" . implode( "\n", $figs ) . "\n</div>\n";

// The slider sat between the AIVA quote and Nathan's quote.
$marker = '“We’re pleased to be named';
$pos    = strpos( $post->post_content, $marker );
if ( false === $pos ) WP_CLI::error( 'insertion point not found' );
wp_update_post( array( 'ID' => $post->ID, 'post_content' => substr_replace( $post->post_content, $gallery . "\n", $pos, 0 ) ) );
WP_CLI::success( 'screenshots restored in post ' . $post->ID );
