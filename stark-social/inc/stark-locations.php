<?php
/**
 * Stark 27 location pages (v27.0.5)
 * One page per service + market, nested under the service page:
 * /web-design/antelope-valley/. Service content comes from the parent;
 * acf-json/group_stark_location.json overlays local hero copy, a local
 * credibility block, local case studies and local FAQ.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Parent service page for a location page, or null.
 */
function stark_get_location_parent( $post = null ) {
    $post = get_post( $post );
    if ( ! $post || 'page' !== $post->post_type || ! $post->post_parent ) return null;
    $parent = get_post( $post->post_parent );
    return stark_is_service_page( $parent ) ? $parent : null;
}

/**
 * Split a one-per-line textarea into trimmed, non-empty lines.
 */
function stark_lines( $text ) {
    return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

/**
 * Service context from the parent page, overlaid with this page's local fields.
 */
function stark_location_context( $location_id ) {
    $parent = stark_get_location_parent( $location_id );
    $ctx    = stark_service_context( $parent->ID );
    $market = stark_field( 'market_name', $location_id ) ?: get_the_title( $location_id );

    $ctx['market']  = $market;
    // Local overlay copy is still [SAMPLE], so a location page is Draft even when its service is Live-sourced.
    if ( 'approved' !== $ctx['copy_status'] ) { $ctx['copy_status'] = 'draft'; $ctx['draft'] = true; }
    $ctx['eyebrow'] = $ctx['service_name'] . ' · ' . $market;
    $ctx['title']   = stark_field( 'hero_headline', $location_id ) ?: $ctx['service_name'] . ' in ' . $market;
    $ctx['thesis']  = stark_field( 'hero_thesis', $location_id ) ?: $ctx['thesis'];

    $cta = stark_link( stark_field( 'hero_cta_primary', $location_id ) );
    if ( $cta ) $ctx['cta_primary'] = $cta;

    $heading = stark_field( 'cred_heading', $location_id );
    $body    = stark_field( 'cred_body', $location_id );
    if ( $heading || $body ) {
        $ctx['credibility'] = array(
            'heading'    => $heading ?: 'Local to ' . $market,
            'body'       => $body,
            'stats'      => array_values( array_filter( (array) stark_field( 'cred_stats', $location_id ), function ( $row ) {
                return ! empty( $row['value'] ) && ! empty( $row['label'] );
            } ) ),
            'areas'      => stark_lines( stark_field( 'cred_areas', $location_id ) ),
            'quote'      => stark_field( 'cred_quote', $location_id ),
            'quote_name' => stark_field( 'cred_quote_name', $location_id ),
        );
    }

    $local_cases = array_values( array_filter( (array) stark_field( 'local_case_studies', $location_id ), function ( $row ) {
        return ! empty( $row['headline'] );
    } ) );
    if ( $local_cases ) {
        $ctx['case_studies'] = array_slice( $local_cases, 0, 3 );
        $ctx['case_heading'] = 'Work in ' . $market;
    }

    $local_faq = array_values( array_filter( (array) stark_field( 'local_faq_items', $location_id ), function ( $row ) {
        return ! empty( $row['question'] ) && ! empty( $row['answer'] );
    } ) );
    $ctx['faq'] = array_merge( $ctx['faq'], $local_faq );

    $ctx['related'] = stark_related_services(
        $parent->ID,
        stark_field( 'related_services', $parent->ID ),
        get_post_field( 'post_name', $location_id )
    );

    $cta_heading = stark_field( 'final_cta_heading', $location_id );
    if ( $cta_heading ) $ctx['final_cta']['title'] = $cta_heading;

    return $ctx;
}

/**
 * Route location pages to templates/stark-location.php.
 */
function stark_location_page_template( $template ) {
    if ( is_page() && stark_get_location_parent( get_queried_object() ) ) {
        $stark_template = get_stylesheet_directory() . '/templates/stark-location.php';
        if ( file_exists( $stark_template ) ) {
            return $stark_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'stark_location_page_template', 99 );

function stark_body_class_location_hero( $classes ) {
    if ( is_page() && stark_get_location_parent( get_queried_object() ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = 'stark-location-page';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_location_hero' );

/**
 * ACF location rule "Stark location page" — any page whose parent is a
 * service page, optionally narrowed to one service.
 */
function stark_register_acf_location_location() {
    if ( ! class_exists( 'ACF_Location' ) ) return;

    class Stark_ACF_Location_Location_Page extends ACF_Location {
        public function initialize() {
            $this->name     = 'stark_location_page';
            $this->label    = 'Stark location page';
            $this->category = 'page';
        }

        public function get_values( $rule ) {
            $values = array( 'any' => 'Any location page' );
            foreach ( stark_get_services() as $slug => $service ) {
                $values[ $slug ] = 'Under ' . $service['name'];
            }
            return $values;
        }

        public function match( $rule, $screen, $field_group ) {
            if ( empty( $screen['post_id'] ) ) return false;
            $parent = stark_get_location_parent( $screen['post_id'] );
            $result = $parent && ( 'any' === $rule['value'] || $parent->post_name === $rule['value'] );
            return '==' === $rule['operator'] ? $result : ! $result;
        }
    }

    acf_register_location_type( 'Stark_ACF_Location_Location_Page' );
}
add_action( 'acf/init', 'stark_register_acf_location_location' );
