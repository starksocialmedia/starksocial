<?php
/**
 * Stark 27 single-purpose pages (v27.0.7 About, v27.0.8 Contact, v27.0.13 Services hub, v27.0.18 Password Generator)
 * Top-level page slug => template, same slug-matching pattern as services.
 * ACF field groups target these pages with the "Stark page" location rule.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Page registry — slug => label + template file in templates/.
 */
function stark_get_page_templates() {
    return apply_filters( 'stark_page_templates', array(
        'about'   => array( 'label' => 'About', 'template' => 'stark-about.php' ),
        'contact' => array( 'label' => 'Contact', 'template' => 'stark-contact.php' ),
        'services' => array( 'label' => 'Services', 'template' => 'stark-services-hub.php' ),
        'support' => array( 'label' => 'Support', 'template' => 'stark-support.php' ),
        'knowledgebase' => array( 'label' => 'Knowledgebase', 'template' => 'stark-support.php', 'parent' => 'support' ),
        'password-generator' => array( 'label' => 'Password Generator', 'template' => 'stark-password-generator.php', 'parent' => 'support' ),
    ) );
}

/**
 * Registry entry for a page, or null. Entries are top-level unless they name
 * a 'parent' slug (e.g. /support/password-generator/).
 */
function stark_page_template_entry( $post = null ) {
    $post = get_post( $post );
    if ( ! $post || 'page' !== $post->post_type ) return null;
    $pages = stark_get_page_templates();
    if ( ! isset( $pages[ $post->post_name ] ) ) return null;
    $entry  = $pages[ $post->post_name ];
    $parent = $post->post_parent ? get_post_field( 'post_name', $post->post_parent ) : '';
    $want   = isset( $entry['parent'] ) ? $entry['parent'] : '';
    return $parent === $want ? $entry : null;
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

/**
 * Default social profiles (same as the footer) and their icons.
 */
function stark_default_social_links() {
    return array(
        array( 'network' => 'instagram', 'url' => 'https://www.instagram.com/starksocial' ),
        array( 'network' => 'linkedin', 'url' => 'https://www.linkedin.com/company/stark-social-media-agency' ),
        array( 'network' => 'facebook', 'url' => 'https://www.facebook.com/starksocial' ),
    );
}

function stark_social_icon( $network ) {
    $paths = array(
        'instagram' => 'M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.805.248 2.228.413.56.217.96.477 1.382.896.419.42.679.819.896 1.38.164.422.36 1.057.413 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.248 1.805-.413 2.227-.217.56-.477.96-.896 1.382-.42.419-.819.679-1.38.896-.422.164-1.057.36-2.227.413-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.805-.248-2.227-.413a3.72 3.72 0 01-1.382-.896 3.72 3.72 0 01-.896-1.38c-.164-.422-.36-1.057-.413-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.17.248-1.805.413-2.228.217-.56.477-.96.896-1.382.42-.419.819-.679 1.38-.896.422-.164 1.057-.36 2.227-.413 1.266-.057 1.646-.07 4.85-.07M12 0C8.741 0 8.332.014 7.052.072 5.775.13 4.902.333 4.14.63a5.58 5.58 0 00-2.021 1.317A5.58 5.58 0 00.63 3.966C.333 4.73.131 5.6.072 6.88.014 8.158 0 8.566 0 12s.014 3.842.072 5.122c.059 1.277.26 2.15.558 2.912.306.788.717 1.459 1.388 2.13a5.58 5.58 0 002.021 1.317c.76.3 1.633.5 2.912.56C8.332 23.986 8.741 24 12 24s3.668-.014 4.948-.072c1.277-.059 2.15-.26 2.912-.558a5.58 5.58 0 002.021-1.317 5.58 5.58 0 001.317-2.021c.3-.763.5-1.635.56-2.912.057-1.28.072-1.688.072-4.948s-.014-3.667-.072-4.947c-.06-1.278-.26-2.15-.558-2.912a5.58 5.58 0 00-1.317-2.021A5.58 5.58 0 0019.86.63c-.763-.297-1.635-.5-2.912-.558C15.667.014 15.258 0 12 0zm0 5.838a6.162 6.162 0 100 12.325 6.162 6.162 0 000-12.325zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z',
        'linkedin'  => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
        'facebook'  => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
    );
    if ( ! isset( $paths[ $network ] ) ) return '';
    return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="' . $paths[ $network ] . '" fill="currentColor"/></svg>';
}
