<?php
/**
 * Stark 27 author pages (v27.0.17) — /author/{nicename}/
 * Profile fields: acf-json/group_stark_author.json (role, highlights, photo,
 * since, podcast host, socials). Bio is the core Biographical Info field.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_is_author_post_list() {
    return is_author() && isset( $_GET['view'] ) && 'posts' === $_GET['view']; // phpcs:ignore WordPress.Security.NonceVerification -- read-only view switch
}

/**
 * /author/{name}/ is the profile; /author/{name}/?view=posts is the paginated
 * article list (blog archive template).
 */
function stark_author_template( $template ) {
    if ( is_author() ) {
        $name = stark_is_author_post_list() ? 'stark-blog-archive.php' : 'stark-author.php';
        $file = get_stylesheet_directory() . '/templates/' . $name;
        if ( file_exists( $file ) ) return $file;
    }
    return $template;
}
add_filter( 'template_include', 'stark_author_template', 99 );

function stark_body_class_author( $classes ) {
    if ( is_author() ) {
        $classes[] = 'stark-has-hero';
        $classes[] = stark_is_author_post_list() ? 'stark-blog-archive' : 'stark-author-page';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_author' );

/**
 * Author profile data.
 */
function stark_author_profile( $user_id ) {
    $acf_id = 'user_' . $user_id;
    $since  = (int) stark_field( 'stark_since', $acf_id );
    $host   = (bool) stark_field( 'stark_podcast_host', $acf_id );
    $episodes = $host
        ? (int) wp_count_posts( 'podcast' )->publish
        : (int) count_user_posts( $user_id, 'podcast', true );

    return array(
        'id'         => $user_id,
        'name'       => get_the_author_meta( 'display_name', $user_id ),
        'first'      => get_the_author_meta( 'first_name', $user_id ) ?: strtok( get_the_author_meta( 'display_name', $user_id ), ' ' ),
        'role'       => stark_field( 'stark_role', $acf_id ),
        'highlights' => array_filter( array_map( 'trim', explode( '·', (string) stark_field( 'stark_highlights', $acf_id ) ) ) ),
        'bio'        => get_the_author_meta( 'description', $user_id ),
        'photo_id'   => (int) stark_field( 'stark_photo', $acf_id ),
        'posts'      => (int) count_user_posts( $user_id, 'post', true ),
        'episodes'   => $episodes,
        'host'       => $host,
        'years'      => $since ? max( 1, (int) wp_date( 'Y' ) - $since ) : 0,
        'since'      => $since,
        'linkedin'   => stark_field( 'stark_linkedin', $acf_id ),
        'instagram'  => stark_field( 'stark_instagram', $acf_id ),
    );
}

/**
 * Profile photo if set, otherwise the core avatar.
 */
function stark_author_avatar( $user_id, $size = 72 ) {
    $photo = (int) stark_field( 'stark_photo', 'user_' . $user_id );
    if ( $photo ) {
        return wp_get_attachment_image( $photo, array( $size * 2, $size * 2 ), false, array( 'width' => $size, 'height' => $size, 'alt' => '', 'loading' => 'lazy' ) );
    }
    return get_avatar( $user_id, $size, '', '' );
}
