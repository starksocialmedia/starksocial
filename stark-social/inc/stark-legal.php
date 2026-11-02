<?php
/**
 * Stark 27 legal pages (v27.0.15)
 * Privacy, Terms, Cookies and Accessibility live under /support/ (same URLs
 * as the live site). Rendered with templates/stark-legal.php and kept out of
 * search results (noindex, follow).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Legal pages in side-nav order — slug => label.
 */
function stark_get_legal_pages() {
    return apply_filters( 'stark_legal_pages', array(
        'privacy-policy'          => 'Privacy Policy',
        'terms-of-service'        => 'Terms of Service',
        'cookie-policy'           => 'Cookie Policy',
        'accessibility-statement' => 'Accessibility Statement',
    ) );
}

function stark_is_legal_page( $post = null ) {
    $post = get_post( $post );
    return $post && 'page' === $post->post_type && array_key_exists( $post->post_name, stark_get_legal_pages() );
}

function stark_legal_template( $template ) {
    if ( is_page() && stark_is_legal_page( get_queried_object() ) ) {
        $file = get_stylesheet_directory() . '/templates/stark-legal.php';
        if ( file_exists( $file ) ) return $file;
    }
    return $template;
}
add_filter( 'template_include', 'stark_legal_template', 99 );

function stark_body_class_legal( $classes ) {
    if ( is_page() && stark_is_legal_page( get_queried_object() ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = 'stark-legal-page';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_legal' );

/**
 * noindex, follow — core robots meta, and RankMath's when it is active.
 */
function stark_legal_wp_robots( $robots ) {
    if ( class_exists( 'RankMath' ) ) return $robots; // RankMath prints its own robots meta
    if ( is_page() && stark_is_legal_page( get_queried_object() ) ) {
        unset( $robots['index'] );
        $robots['noindex'] = true;
        $robots['follow']  = true;
    }
    return $robots;
}
add_filter( 'wp_robots', 'stark_legal_wp_robots', 20 );

function stark_legal_rank_math_robots( $robots ) {
    if ( is_page() && stark_is_legal_page( get_queried_object() ) ) {
        $robots['index']  = 'noindex';
        $robots['follow'] = 'follow';
    }
    return $robots;
}
add_filter( 'rank_math/frontend/robots', 'stark_legal_rank_math_robots' );
