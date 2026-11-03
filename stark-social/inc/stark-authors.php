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
        // v27.3.21 — hero shows the first paragraph; the rest + focus areas sit under "Background".
        'bio_lead'   => trim( (string) preg_split( '/\R\s*\R/', trim( (string) get_the_author_meta( 'description', $user_id ) ), 2 )[0] ),
        'bio_rest'   => trim( (string) ( preg_split( '/\R\s*\R/', trim( (string) get_the_author_meta( 'description', $user_id ) ), 2 )[1] ?? '' ) ),
        'focus_heading' => stark_field( 'stark_focus_heading', $acf_id ),
        'focus_intro'   => stark_field( 'stark_focus_intro', $acf_id ),
        'focus'         => array_values( array_filter( array_map( function ( $line ) {
            $parts = array_map( 'trim', explode( '|', $line, 2 ) );
            return '' === $parts[0] ? null : array( 'title' => $parts[0], 'description' => $parts[1] ?? '' );
        }, stark_lines( stark_field( 'stark_focus', $acf_id ) ) ) ) ),
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

/**
 * Live's team pages (/about/nathan-imhoff/, /about/deanna-l-miller/) → author
 * profiles, plus the pre-v27.0.18 footer URL for the password generator.
 */
function stark_redirect_legacy_profile_urls() {
    if ( ! is_404() ) return;
    $path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
    $map  = apply_filters( 'stark_legacy_profile_redirects', array(
        'about/nathan-imhoff'   => '/author/nateimhoff/',
        'about/deanna-l-miller' => '/author/deannaleigh/',
        'about/deanna-miller'   => '/author/deannaleigh/',
        'password-generator'    => '/support/password-generator/',
    ) );
    if ( isset( $map[ $path ] ) ) {
        wp_safe_redirect( home_url( $map[ $path ] ), 301, 'Stark' );
        exit;
    }
}
add_action( 'template_redirect', 'stark_redirect_legacy_profile_urls', 1 );
