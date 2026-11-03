<?php
/**
 * Stark 27 animated GIFs as video (v27.4.1)
 *
 * Old posts and episodes use 2–7 MB animated GIFs. tools/images/convert-gifs.sh
 * writes a silent MP4 next to each large GIF; this filter swaps the <img> for a
 * looping, muted <video> wherever that MP4 exists, with a link to the GIF as
 * the fallback (an <img> fallback would still be downloaded). Content in the
 * database isn't changed, so it survives the cutover re-sync from live, and
 * deleting an MP4 brings its GIF back.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_gif_to_video( $content ) {
    if ( false === stripos( $content, '.gif' ) ) return $content;
    $uploads = wp_get_upload_dir();
    $base_url = wp_make_link_relative( $uploads['baseurl'] );

    return preg_replace_callback( '/<img\b[^>]*\bsrc="([^"]+\.gif)"[^>]*>/i', function ( $m ) use ( $uploads, $base_url ) {
        $path = wp_make_link_relative( $m[1] );
        if ( 0 !== strpos( $path, $base_url . '/' ) ) return $m[0];
        $file = $uploads['basedir'] . substr( preg_replace( '/\.gif$/i', '.mp4', $path ), strlen( $base_url ) );
        if ( ! is_file( $file ) ) return $m[0];

        $attr = function ( $name ) use ( $m ) {
            return preg_match( '/\b' . $name . '="([^"]*)"/i', $m[0], $a ) ? $a[1] : '';
        };
        $alt   = $attr( 'alt' );
        $w     = $attr( 'width' );
        $h     = $attr( 'height' );
        if ( ! $w || ! $h ) {
            // Reserve the space (CLS) when the <img> had no dimensions.
            $size = @getimagesize( substr( $file, 0, -4 ) . '.gif' );
            if ( $size ) { $w = $size[0]; $h = $size[1]; }
        }
        $class = trim( $attr( 'class' ) . ' stark-gif-video' );
        $mp4   = preg_replace( '/\.gif$/i', '.mp4', $m[1] );

        return sprintf(
            '<video class="%s" autoplay loop muted playsinline preload="metadata"%s%s%s>'
            . '<source src="%s" type="video/mp4"><a href="%s">%s</a></video>',
            esc_attr( $class ),
            $w ? ' width="' . esc_attr( $w ) . '"' : '',
            $h ? ' height="' . esc_attr( $h ) . '"' : '',
            ' aria-label="' . esc_attr( '' !== $alt ? $alt : __( 'Animated image', 'stark-social' ) ) . '"',
            esc_url( $mp4 ),
            esc_url( $m[1] ),
            esc_html( '' !== $alt ? $alt : __( 'View the animation', 'stark-social' ) )
        );
    }, $content );
}
add_filter( 'the_content', 'stark_gif_to_video', 20 );
