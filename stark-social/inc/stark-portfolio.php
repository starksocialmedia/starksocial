<?php
/**
 * Stark 27 portfolio (v27.0.12)
 * Case studies as the `portfolio` post type. Archive at /work/ (the nav item),
 * singles at /portfolio/{slug}/ — the same URLs as the live site.
 * Fields: acf-json/group_stark_portfolio.json.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'STARK_PORTFOLIO_REWRITE_VERSION', '1' );

function stark_register_portfolio() {
    register_post_type( 'portfolio', array(
        'labels' => array(
            'name'               => 'Portfolio',
            'singular_name'      => 'Case Study',
            'menu_name'          => 'Portfolio',
            'add_new_item'       => 'Add New Case Study',
            'edit_item'          => 'Edit Case Study',
            'new_item'           => 'New Case Study',
            'view_item'          => 'View Case Study',
            'view_items'         => 'View Work',
            'search_items'       => 'Search Case Studies',
            'not_found'          => 'No case studies found.',
            'all_items'          => 'All Case Studies',
            'archives'           => 'Work',
        ),
        'public'       => true,
        'has_archive'  => 'work',
        'rewrite'      => array( 'slug' => 'portfolio', 'with_front' => false ),
        'menu_icon'    => 'dashicons-portfolio',
        'menu_position'=> 21,
        'supports'     => array( 'title', 'thumbnail', 'excerpt', 'revisions' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'stark_register_portfolio' );

/**
 * Flush rewrites once per rewrite version, so /work/ and /portfolio/ resolve
 * on any environment without a manual Permalinks save.
 */
function stark_portfolio_maybe_flush() {
    if ( get_option( 'stark_portfolio_rewrite_version' ) !== STARK_PORTFOLIO_REWRITE_VERSION ) {
        flush_rewrite_rules( false );
        update_option( 'stark_portfolio_rewrite_version', STARK_PORTFOLIO_REWRITE_VERSION );
    }
}
add_action( 'init', 'stark_portfolio_maybe_flush', 99 );

function stark_portfolio_template( $template ) {
    $file = null;
    if ( is_post_type_archive( 'portfolio' ) ) {
        $file = 'stark-work-archive.php';
    } elseif ( is_singular( 'portfolio' ) ) {
        $file = 'stark-work-single.php';
    }
    if ( $file && file_exists( get_stylesheet_directory() . '/templates/' . $file ) ) {
        return get_stylesheet_directory() . '/templates/' . $file;
    }
    return $template;
}
add_filter( 'template_include', 'stark_portfolio_template', 99 );

function stark_body_class_portfolio( $classes ) {
    if ( is_post_type_archive( 'portfolio' ) || is_singular( 'portfolio' ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = is_singular( 'portfolio' ) ? 'stark-work-single' : 'stark-work-archive';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_portfolio' );

/**
 * Order by project year without dropping items that have no year set.
 */
function stark_portfolio_year_meta_query() {
    return array(
        'relation'   => 'OR',
        'stark_year' => array( 'key' => 'project_year', 'compare' => 'EXISTS', 'type' => 'NUMERIC' ),
        'no_year'    => array( 'key' => 'project_year', 'compare' => 'NOT EXISTS' ),
    );
}

/**
 * All case studies on the archive, newest project first.
 */
function stark_portfolio_archive_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'portfolio' ) ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'meta_query', stark_portfolio_year_meta_query() );
        $query->set( 'orderby', array( 'stark_year' => 'DESC', 'date' => 'DESC' ) );
    }
}
add_action( 'pre_get_posts', 'stark_portfolio_archive_query' );

/**
 * Normalised data for a case study — shared by archive cards, the single,
 * and the homepage / service-page teasers.
 */
function stark_portfolio_data( $post = null ) {
    $post = get_post( $post );
    $id   = $post->ID;

    $results = array_values( array_filter( (array) stark_field( 'results', $id ), function ( $row ) {
        return ! empty( $row['value'] ) && ! empty( $row['label'] );
    } ) );

    $gallery = array_values( array_filter( array_map( function ( $row ) {
        return ! empty( $row['image']['ID'] ) ? $row['image'] : null;
    }, (array) stark_field( 'gallery', $id ) ) ) );

    $hero = stark_field( 'hero_image', $id );
    $hero_id = ! empty( $hero['ID'] ) ? $hero['ID'] : ( get_post_thumbnail_id( $id ) ?: ( $gallery ? $gallery[0]['ID'] : 0 ) );

    $services = array();
    $registry = stark_get_services();
    foreach ( (array) stark_field( 'services', $id ) as $slug ) {
        if ( isset( $registry[ $slug ] ) ) $services[ $slug ] = $registry[ $slug ]['name'];
    }

    $testimonial = (array) stark_field( 'testimonial', $id );

    return array(
        'id'          => $id,
        'title'       => get_the_title( $id ),
        'url'         => get_permalink( $id ),
        'client'      => stark_field( 'client_name', $id ) ?: get_the_title( $id ),
        'summary'     => stark_field( 'summary', $id ) ?: get_the_excerpt( $id ),
        'industry'    => stark_field( 'industry', $id ),
        'year'        => stark_field( 'project_year', $id ),
        'services'    => $services,
        'website'     => stark_field( 'external_url', $id ),
        'overview'    => stark_field( 'overview', $id ),
        'challenge'   => stark_field( 'challenge', $id ),
        'approach'    => stark_field( 'approach', $id ),
        'outcome'     => stark_field( 'results_summary', $id ),
        'results'     => array_slice( $results, 0, 3 ),
        'hero_id'     => $hero_id,
        'card_id'     => get_post_thumbnail_id( $id ) ?: $hero_id,
        'gallery'     => $gallery,
        'testimonial' => ! empty( $testimonial['quote'] ) ? $testimonial : null,
    );
}

/**
 * Most recent case study (by project year) — homepage / service fallback teaser.
 */
function stark_latest_portfolio_item() {
    $items = get_posts( array(
        'post_type'   => 'portfolio',
        'numberposts' => 1,
        'meta_query'  => stark_portfolio_year_meta_query(),
        'orderby'     => array( 'stark_year' => 'DESC', 'date' => 'DESC' ),
    ) );
    return $items ? $items[0] : null;
}

/**
 * Filter script for the /work/ archive.
 */
function stark_enqueue_work_filter() {
    if ( ! is_post_type_archive( 'portfolio' ) ) return;
    wp_enqueue_script(
        'stark-work-filter',
        get_stylesheet_directory_uri() . '/js/stark-work-filter.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/stark-work-filter.js' ),
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}
add_action( 'wp_enqueue_scripts', 'stark_enqueue_work_filter' );
