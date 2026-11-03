<?php
/**
 * Stark 27 site tweaks (v27.3.19) — the live WPCode snippets that Phase 2 still
 * needs, as theme code (Phase 2 has no WPCode snippets). Live snippet IDs noted.
 * Not ported, on purpose: Work CPT / taxonomies / portfolio shortcodes, nav and
 * breadcrumbs (replaced by Phase 2), Blog Player (unused in content), Copyright
 * and Reading Time shortcodes (templates do this), Classic Editor and "hide all
 * admin notices" (editorial choices for Nathan), Media filename/alt-field editors.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* 831 — comments and trackbacks off everywhere (as on live). */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );
add_action( 'admin_init', function () {
    foreach ( get_post_types() as $type ) {
        if ( post_type_supports( $type, 'comments' ) ) {
            remove_post_type_support( $type, 'comments' );
            remove_post_type_support( $type, 'trackbacks' );
        }
    }
} );
add_action( 'admin_menu', function () { remove_menu_page( 'edit-comments.php' ); } );
add_action( 'wp_before_admin_bar_render', function () { global $wp_admin_bar; $wp_admin_bar->remove_menu( 'comments' ); } );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

/* 816 — lowercase upload filenames. */
add_filter( 'sanitize_file_name', 'mb_strtolower' );

/* 829 — SVG uploads, administrators only (live allowed every role). */
add_filter( 'upload_mimes', function ( $mimes ) {
    if ( current_user_can( 'manage_options' ) ) $mimes['svg'] = 'image/svg+xml';
    return $mimes;
} );
