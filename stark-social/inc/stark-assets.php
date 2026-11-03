<?php
/**
 * Stark 27 cache-proof asset URLs (v27.3.12)
 *
 * Cloudflare on starksocial.com caches static files for a year and ignores
 * query strings, so `custom.css?ver=…` kept serving an August 2026 copy after
 * deploys. Theme CSS/JS is therefore served from content-hashed copies:
 * wp-content/uploads/stark-assets/{name}-{hash}.{ext}. A changed file gets a
 * new name, so no cache can serve it stale. Copies are made on the first
 * request after a deploy; older copies of the same file are removed.
 * Relative url() references in CSS (fonts/…) are rewritten to absolute theme URLs.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_hashed_asset_src( $src ) {
    $theme_uri = get_stylesheet_directory_uri();
    if ( ! is_string( $src ) || 0 !== strpos( $src, $theme_uri ) ) return $src;

    $rel  = ltrim( strtok( substr( $src, strlen( $theme_uri ) ), '?' ), '/' );
    $file = get_stylesheet_directory() . '/' . $rel;
    $ext  = strtolower( pathinfo( $rel, PATHINFO_EXTENSION ) );
    if ( ! in_array( $ext, array( 'css', 'js' ), true ) || ! is_file( $file ) ) return $src;

    $uploads = wp_get_upload_dir();
    if ( ! empty( $uploads['error'] ) ) return $src;
    $dir  = trailingslashit( $uploads['basedir'] ) . 'stark-assets';
    $base = str_replace( '/', '-', substr( $rel, 0, -strlen( $ext ) - 1 ) );
    $hash = substr( md5( $rel . filemtime( $file ) . filesize( $file ) ), 0, 10 );
    $name = "$base-$hash.$ext";

    if ( ! is_file( "$dir/$name" ) ) {
        if ( ! wp_mkdir_p( $dir ) ) return $src;
        $body = file_get_contents( $file );
        if ( 'css' === $ext ) {
            $from = trailingslashit( $theme_uri . '/' . dirname( $rel ) );
            $from = str_replace( '/./', '/', $from );
            $body = preg_replace_callback( '/url\(\s*([\'"]?)(?!data:|https?:|\/)([^\'")]+)\1\s*\)/i', function ( $m ) use ( $from ) {
                return 'url(' . $m[1] . $from . $m[2] . $m[1] . ')';
            }, $body );
        }
        if ( false === file_put_contents( "$dir/$name.tmp", $body ) || ! rename( "$dir/$name.tmp", "$dir/$name" ) ) return $src;
        foreach ( (array) glob( "$dir/$base-*.$ext" ) as $old ) {
            if ( basename( $old ) !== $name && preg_match( '/^' . preg_quote( $base, '/' ) . '-[0-9a-f]{10}\.' . $ext . '$/', basename( $old ) ) ) @unlink( $old );
        }
    }
    return trailingslashit( $uploads['baseurl'] ) . "stark-assets/$name";
}
add_filter( 'style_loader_src', 'stark_hashed_asset_src', 20 );
add_filter( 'script_loader_src', 'stark_hashed_asset_src', 20 );
