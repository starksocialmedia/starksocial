<?php
/**
 * Stark 27 social proof store (v27.3.0) — testimonials, awards, press.
 * Only what exists on the live site. As of November 2 2026 live has no
 * testimonials and no client or award logo files; add them here as they come
 * in and templates pick them up.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Testimonials: quote, name, title, company, service (registry slug), year,
 * portfolio (case-study slug, optional).
 */
function stark_get_testimonials( $service = null ) {
    $items = apply_filters( 'stark_testimonials', array() );
    if ( $service ) {
        $items = array_values( array_filter( $items, function ( $t ) use ( $service ) { return ( $t['service'] ?? '' ) === $service; } ) );
    }
    return $items;
}

/**
 * Awards, with the post that announced each one.
 */
function stark_get_awards() {
    return apply_filters( 'stark_awards', array(
        array(
            'title'  => 'Communicator Award — Excellence in Web Design',
            'org'    => 'Academy of Interactive and Visual Arts (23rd Annual Communicator Awards)',
            'year'   => 2017,
            'for'    => 'Valencia Jazz & Blues Concert Series website',
            'post'   => 'stark-social-wins-excellence-in-web-design-award-for-valencia-jazz-blues',
            'case'   => 'valencia-jazz-blues',
        ),
        array(
            'title'  => 'Best of SCV — Web Design Company',
            'org'    => 'The Santa Clarita Valley Signal',
            'year'   => 2015,
            'for'    => '',
            'post'   => 'stark-social-media-agency-named-best-santa-clarita-web-design-company-in-2015',
            'case'   => '',
        ),
    ) );
}

/**
 * Press: none on live beyond the award announcements above.
 */
function stark_get_press() {
    return apply_filters( 'stark_press', array() );
}

/**
 * Permalink for an award's announcement post, or ''.
 */
function stark_award_url( $award ) {
    $post = $award['post'] ? get_page_by_path( $award['post'], OBJECT, 'post' ) : null;
    return $post ? get_permalink( $post ) : '';
}
