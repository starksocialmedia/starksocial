<?php
/**
 * Stark 27 single-purpose pages (v27.0.7+)
 * Top-level page slug => template, same slug-matching pattern as services.
 * ACF field groups target these pages with the "Stark page" location rule.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Page registry — slug => label + template file in templates/.
 */
function stark_get_page_templates() {
    return apply_filters( 'stark_page_templates', array(
        'about' => array( 'label' => 'About', 'template' => 'stark-about.php' ),
    ) );
}

/**
 * Registry entry for a top-level page, or null.
 */
function stark_page_template_entry( $post = null ) {
    $post = get_post( $post );
    if ( ! $post || 'page' !== $post->post_type || 0 !== (int) $post->post_parent ) return null;
    $pages = stark_get_page_templates();
    return isset( $pages[ $post->post_name ] ) ? $pages[ $post->post_name ] : null;
}

function stark_registered_page_template( $template ) {
    if ( ! is_page() ) return $template;
    $entry = stark_page_template_entry( get_queried_object() );
    if ( $entry ) {
        $stark_template = get_stylesheet_directory() . '/templates/' . $entry['template'];
        if ( file_exists( $stark_template ) ) {
            return $stark_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'stark_registered_page_template', 99 );

function stark_body_class_registered_page( $classes ) {
    if ( is_page() && stark_page_template_entry( get_queried_object() ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = 'stark-page-' . get_queried_object()->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_registered_page' );

/**
 * ACF location rule "Stark page" — matches a registered page by slug.
 */
function stark_register_acf_page_location() {
    if ( ! class_exists( 'ACF_Location' ) ) return;

    class Stark_ACF_Location_Page extends ACF_Location {
        public function initialize() {
            $this->name     = 'stark_page';
            $this->label    = 'Stark page';
            $this->category = 'page';
        }

        public function get_values( $rule ) {
            $values = array();
            foreach ( stark_get_page_templates() as $slug => $page ) {
                $values[ $slug ] = $page['label'] . ' (/' . $slug . '/)';
            }
            return $values;
        }

        public function match( $rule, $screen, $field_group ) {
            if ( empty( $screen['post_id'] ) ) return false;
            $post   = get_post( $screen['post_id'] );
            $result = stark_page_template_entry( $post ) && $post->post_name === $rule['value'];
            return '==' === $rule['operator'] ? $result : ! $result;
        }
    }

    acf_register_location_type( 'Stark_ACF_Location_Page' );
}
add_action( 'acf/init', 'stark_register_acf_page_location' );
