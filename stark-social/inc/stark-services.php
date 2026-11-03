<?php
/**
 * Stark 27 service pages (v27.0.4)
 * Service registry, template routing, ACF location rule, page context, icons.
 * Field group lives in acf-json/group_stark_service.json.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Service registry — slug => name + one-line description.
 * Slugs drive template routing and the ACF location rule.
 */
function stark_get_services() {
    return apply_filters( 'stark_services', array(
        'social-media-management' => array( 'name' => 'Social Media Management', 'desc' => 'Content calendar, posting, reporting.', 'icon' => 'megaphone' ),
        'paid-advertising'        => array( 'name' => 'Paid Advertising', 'desc' => 'Google, Meta, LinkedIn paid campaigns.', 'icon' => 'target' ),
        'web-design'              => array( 'name' => 'Web Design', 'desc' => 'Custom WordPress sites built to convert.', 'icon' => 'layout' ),
        'seo'                     => array( 'name' => 'SEO', 'desc' => 'Technical and local SEO that ranks.', 'icon' => 'search' ),
        'content-creation'        => array( 'name' => 'Content Creation', 'desc' => 'Photo, video, graphic, written.', 'icon' => 'camera' ),
        'brand-strategy-identity' => array( 'name' => 'Brand Strategy', 'desc' => 'Positioning and visual identity.', 'icon' => 'pen' ),
        'audit-consulting'        => array( 'name' => 'Audit & Consulting', 'desc' => 'Marketing audits for small to mid business.', 'icon' => 'chart' ),
    ) );
}

/**
 * Top-level page whose slug is a registered service.
 */
function stark_is_service_page( $post = null ) {
    $post = get_post( $post );
    return $post
        && 'page' === $post->post_type
        && 0 === (int) $post->post_parent
        && array_key_exists( $post->post_name, stark_get_services() );
}

/**
 * ACF getter that degrades to null when ACF Pro is inactive.
 */
function stark_field( $name, $post_id ) {
    return function_exists( 'get_field' ) ? get_field( $name, $post_id ) : null;
}

/**
 * Normalise an ACF link field (or fallback array) to url/title/target.
 */
function stark_link( $link, $fallback = null ) {
    if ( empty( $link['url'] ) ) $link = $fallback;
    if ( empty( $link['url'] ) ) return null;
    return array(
        'url'    => $link['url'],
        'title'  => ! empty( $link['title'] ) ? $link['title'] : 'Learn more',
        'target' => ! empty( $link['target'] ) ? $link['target'] : '',
    );
}

/**
 * Build the render context for a service page from its ACF fields.
 * Location pages (inc/stark-locations.php) start from this and overlay.
 */
function stark_service_context( $service_id ) {
    $slug     = get_post_field( 'post_name', $service_id );
    $services = stark_get_services();

    $included = array_values( array_filter( (array) stark_field( 'included_items', $service_id ), function ( $row ) {
        return ! empty( $row['title'] );
    } ) );

    $process = array_values( array_filter( (array) stark_field( 'process_steps', $service_id ), function ( $row ) {
        return ! empty( $row['title'] );
    } ) );

    $faq = array_values( array_filter( (array) stark_field( 'faq_items', $service_id ), function ( $row ) {
        return ! empty( $row['question'] ) && ! empty( $row['answer'] );
    } ) );

    $case = stark_field( 'case_study', $service_id );
    $case_studies = ! empty( $case['headline'] ) ? array( $case ) : stark_service_portfolio_teasers( $slug, 1 );

    $cta = (array) stark_field( 'final_cta', $service_id );

    // v27.3.0 — sections carried over from the live Phase 1 pages.
    $titled = function ( $rows ) {
        return array_values( array_filter( (array) $rows, function ( $row ) { return ! empty( $row['title'] ); } ) );
    };
    $extra = array();
    foreach ( (array) stark_field( 'extra_sections', $service_id ) as $section ) {
        if ( empty( $section['heading'] ) ) continue;
        $section['items'] = $titled( $section['items'] ?? array() );
        $extra[] = $section;
    }
    $status = stark_field( 'copy_status', $service_id ) ?: 'draft';

    return array(
        'service_id'       => $service_id,
        'service_slug'     => $slug,
        'service_name'     => get_the_title( $service_id ),
        'market'           => null,
        'draft'            => 'approved' !== $status,
        'copy_status'      => $status,
        'eyebrow'          => stark_field( 'hero_eyebrow', $service_id ) ?: 'Services',
        'title'            => stark_field( 'hero_headline', $service_id ) ?: get_the_title( $service_id ),
        'thesis'           => stark_field( 'hero_thesis', $service_id ),
        'hero_note'        => stark_field( 'hero_note', $service_id ),
        'outcomes'         => array(
            'eyebrow' => stark_field( 'outcomes_eyebrow', $service_id ) ?: 'Outcomes',
            'heading' => stark_field( 'outcomes_heading', $service_id ),
            'intro'   => stark_field( 'outcomes_intro', $service_id ),
            'items'   => $titled( stark_field( 'outcomes_items', $service_id ) ),
            'note'    => stark_field( 'outcomes_note', $service_id ),
        ),
        'problem'          => array(
            'eyebrow' => stark_field( 'problem_eyebrow', $service_id ) ?: 'The reality',
            'heading' => stark_field( 'problem_heading', $service_id ),
            'body'    => array_values( array_filter( array_map( 'trim', preg_split( '/\R\s*\R/', (string) stark_field( 'problem_body', $service_id ) ) ) ) ),
            'points'  => stark_lines( stark_field( 'problem_points', $service_id ) ),
            'note'    => stark_field( 'problem_note', $service_id ),
        ),
        'fit'              => array(
            'eyebrow'      => stark_field( 'fit_eyebrow', $service_id ) ?: 'Who it’s for',
            'heading'      => stark_field( 'fit_heading', $service_id ),
            'intro'        => stark_field( 'fit_intro', $service_id ),
            'good_heading' => stark_field( 'fit_good_heading', $service_id ) ?: 'You’re a good fit if…',
            'good'         => stark_lines( stark_field( 'fit_good', $service_id ) ),
            'bad_heading'  => stark_field( 'fit_bad_heading', $service_id ) ?: 'This is probably not a fit if…',
            'bad'          => stark_lines( stark_field( 'fit_bad', $service_id ) ),
        ),
        'extra_sections'   => $extra,
        'included_intro'   => stark_field( 'included_intro', $service_id ),
        'included_note'    => stark_field( 'included_note', $service_id ),
        'process_eyebrow'  => stark_field( 'process_eyebrow', $service_id ) ?: 'Our process',
        'process_intro'    => stark_field( 'process_intro', $service_id ),
        'process_note'     => stark_field( 'process_note', $service_id ),
        'faq_intro'        => stark_field( 'faq_intro', $service_id ),
        'cta_primary'      => stark_link( stark_field( 'hero_cta_primary', $service_id ), array( 'url' => '/contact/', 'title' => 'Start a project' ) ),
        'cta_secondary'    => stark_link( stark_field( 'hero_cta_secondary', $service_id ), $included ? array( 'url' => '#stark-included', 'title' => "See what's included" ) : null ),
        'credibility'      => null,
        'included_heading' => stark_field( 'included_heading', $service_id ) ?: "What's included",
        'included'         => array_slice( $included, 0, 8 ),
        'process_heading'  => stark_field( 'process_heading', $service_id ) ?: 'How we work',
        'process'          => array_slice( $process, 0, 4 ),
        'case_heading'     => 'Featured work',
        'case_studies'     => $case_studies,
        'faq_heading'      => stark_field( 'faq_heading', $service_id ) ?: 'Questions, answered',
        'faq'              => $faq,
        'related'          => stark_related_services( $service_id, stark_field( 'related_services', $service_id ) ),
        'final_cta'        => array(
            'title'  => ! empty( $cta['heading'] ) ? $cta['heading'] : 'Ready to grow?',
            'sub'    => ! empty( $cta['subhead'] ) ? $cta['subhead'] : "Tell us where you want to be. We'll show you how we'd get there.",
            'button' => stark_link( isset( $cta['button'] ) ? $cta['button'] : null, array( 'url' => '/contact/', 'title' => 'Get in touch' ) ),
            'note'   => ! empty( $cta['note'] ) ? $cta['note'] : '',
        ),
    );
}

/**
 * Three related service cards. Uses the editor's picks when set, otherwise
 * the next three services in registry order. With $market, links to the
 * same market under each service when that page exists.
 */
function stark_related_services( $service_id, $picked = array(), $market = null ) {
    $services = stark_get_services();
    $slugs    = array();

    foreach ( (array) $picked as $page ) {
        $page = get_post( $page );
        if ( $page && stark_is_service_page( $page ) && $page->ID !== $service_id ) {
            $slugs[] = $page->post_name;
        }
    }

    if ( ! $slugs ) {
        $keys  = array_keys( $services );
        $index = array_search( get_post_field( 'post_name', $service_id ), $keys, true );
        $index = false === $index ? -1 : $index;
        for ( $i = 1; count( $slugs ) < 3 && $i < count( $keys ); $i++ ) {
            $slugs[] = $keys[ ( $index + $i ) % count( $keys ) ];
        }
    }

    $cards = array();
    foreach ( array_slice( $slugs, 0, 3 ) as $slug ) {
        $page = get_page_by_path( $slug );
        $desc = $page ? stark_field( 'card_description', $page->ID ) : '';
        $url  = '/' . $slug . '/';

        if ( $market && get_page_by_path( $slug . '/' . $market ) ) {
            $url = '/' . $slug . '/' . $market . '/';
        }

        $cards[] = array(
            'name' => $page ? get_the_title( $page ) : $services[ $slug ]['name'],
            'desc' => $desc ?: $services[ $slug ]['desc'],
            'url'  => $url,
            'icon' => $services[ $slug ]['icon'],
        );
    }
    return $cards;
}

/**
 * Inline stroke icons for "What's included" (24x24, currentColor).
 * Choices mirror the included_items > icon select in the field group.
 */
function stark_service_icon( $name ) {
    $paths = array(
        'check'     => '<path d="M20 6 9 17l-5-5"/>',
        'chart'     => '<path d="M3 3v18h18"/><path d="M7 15v3M12 10v8M17 6v12"/>',
        'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'pen'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'camera'    => '<path d="M4 7h3l2-3h6l2 3h3a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13" r="4"/>',
        'megaphone' => '<path d="M3 11v2a1 1 0 0 0 1 1h3l9 5V5L7 10H4a1 1 0 0 0-1 1Z"/><path d="M20 9a4 4 0 0 1 0 6"/>',
        'layout'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
        'users'     => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0"/><path d="M16 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6.3"/>',
        'shield'    => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6Z"/>',
    );
    $path = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['check'];
    return '<svg class="stark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $path . '</svg>';
}

/**
 * Route service pages to templates/stark-service.php.
 */
function stark_service_page_template( $template ) {
    if ( is_page() && stark_is_service_page( get_queried_object() ) ) {
        $stark_template = get_stylesheet_directory() . '/templates/stark-service.php';
        if ( file_exists( $stark_template ) ) {
            return $stark_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'stark_service_page_template', 99 );

function stark_body_class_service_hero( $classes ) {
    if ( is_page() && stark_is_service_page( get_queried_object() ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = 'stark-service-page';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_service_hero' );

/**
 * ACF location rule "Stark service page" — matches by page slug so the
 * field group in acf-json/ follows the registry, not hard-coded page IDs.
 */
function stark_register_acf_service_location() {
    if ( ! class_exists( 'ACF_Location' ) ) return;

    class Stark_ACF_Location_Service_Page extends ACF_Location {
        public function initialize() {
            $this->name     = 'stark_service_page';
            $this->label    = 'Stark service page';
            $this->category = 'page';
        }

        public function get_values( $rule ) {
            $values = array( 'any' => 'Any service page' );
            foreach ( stark_get_services() as $slug => $service ) {
                $values[ $slug ] = $service['name'] . ' (/' . $slug . '/)';
            }
            return $values;
        }

        public function match( $rule, $screen, $field_group ) {
            if ( empty( $screen['post_id'] ) ) return false;
            $post = get_post( $screen['post_id'] );
            $result = stark_is_service_page( $post )
                && ( 'any' === $rule['value'] || $post->post_name === $rule['value'] );
            return '==' === $rule['operator'] ? $result : ! $result;
        }
    }

    acf_register_location_type( 'Stark_ACF_Location_Service_Page' );
}
add_action( 'acf/init', 'stark_register_acf_service_location' );

/**
 * Limit the Related services picker to top-level service pages.
 */
function stark_related_services_query( $args ) {
    $args['post_name__in'] = array_keys( stark_get_services() );
    $args['post_parent']   = 0;
    return $args;
}
add_filter( 'acf/fields/relationship/query/name=related_services', 'stark_related_services_query' );

/**
 * Case study teasers from the portfolio for a service: most recent items
 * tagged with $slug, mapped to the case_study field shape.
 */
function stark_service_portfolio_teasers( $slug, $count = 1 ) {
    if ( ! post_type_exists( 'portfolio' ) ) return array();
    $items = get_posts( array(
        'post_type'   => 'portfolio',
        'numberposts' => $count,
        'meta_query'  => array(
            'relation' => 'AND',
            array( 'key' => 'services', 'value' => '"' . $slug . '"', 'compare' => 'LIKE' ),
            stark_portfolio_year_meta_query(),
        ),
        'orderby'     => array( 'stark_year' => 'DESC', 'date' => 'DESC' ),
    ) );
    return array_map( 'stark_portfolio_teaser', $items );
}

/**
 * A portfolio item in the case_study teaser shape (templates/parts/service-case-study.php).
 */
function stark_portfolio_teaser( $item ) {
    $work = stark_portfolio_data( $item );
    $stat = $work['results'] ? $work['results'][0] : array( 'value' => '', 'label' => '' );
    return array(
        'client'       => $work['client'],
        'headline'     => $work['summary'] ?: $work['title'],
        'result_value' => $stat['value'],
        'result_label' => $stat['label'],
        'summary'      => '',
        'image'        => $work['card_id'] ? array( 'ID' => $work['card_id'] ) : null,
        'link'         => array( 'url' => $work['url'], 'title' => 'Read the case study', 'target' => '' ),
    );
}

/**
 * Live served services at /services/{slug}/. Phase 2 moves them to /{slug}/ —
 * 301 the old URLs (only when nothing else answers at that path).
 */
function stark_redirect_legacy_service_urls() {
    if ( ! is_404() ) return;
    $path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
    if ( preg_match( '#^services/([a-z0-9-]+)(?:/([a-z0-9-]+))?$#', $path, $m ) && array_key_exists( $m[1], stark_get_services() ) ) {
        $target = '/' . $m[1] . '/' . ( ! empty( $m[2] ) ? $m[2] . '/' : '' );
        wp_safe_redirect( home_url( $target ), 301, 'Stark' );
        exit;
    }
}
add_action( 'template_redirect', 'stark_redirect_legacy_service_urls', 1 );
