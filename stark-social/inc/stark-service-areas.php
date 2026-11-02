<?php
/**
 * Stark 27 service areas (v27.0.6)
 * Market registry + /service-areas/ hub routing. Hub is a stub for now.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Market registry — slug => name + brief context. Slugs are the {area}
 * segment of /{service}/{area}/ location pages.
 * Placeholder context — Nathan writes real copy when the hub is built.
 */
function stark_get_markets() {
    return apply_filters( 'stark_markets', array(
        'santa-clarita'       => array( 'name' => 'Santa Clarita', 'desc' => 'Our home base. Valencia, Saugus, Canyon Country, Newhall, Stevenson Ranch.' ),
        'san-fernando-valley' => array( 'name' => 'San Fernando Valley', 'desc' => 'Just over the pass, from Burbank to Chatsworth.' ),
        'antelope-valley'     => array( 'name' => 'Antelope Valley', 'desc' => 'Lancaster, Palmdale and the high desert communities.' ),
        'ventura-county'      => array( 'name' => 'Ventura County', 'desc' => 'Simi Valley, Thousand Oaks, Oxnard and Ventura.' ),
        'bakersfield'         => array( 'name' => 'Bakersfield', 'desc' => 'Kern County businesses ready to grow online.' ),
    ) );
}

function stark_is_service_areas_page( $post = null ) {
    $post = get_post( $post );
    return $post && 'page' === $post->post_type && 0 === (int) $post->post_parent && 'service-areas' === $post->post_name;
}

/**
 * Route /service-areas/ to templates/stark-service-areas.php.
 */
function stark_service_areas_template( $template ) {
    if ( is_page() && stark_is_service_areas_page( get_queried_object() ) ) {
        $stark_template = get_stylesheet_directory() . '/templates/stark-service-areas.php';
        if ( file_exists( $stark_template ) ) {
            return $stark_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'stark_service_areas_template', 99 );

function stark_body_class_service_areas( $classes ) {
    if ( is_page() && stark_is_service_areas_page( get_queried_object() ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = 'stark-service-areas-page';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_service_areas' );
