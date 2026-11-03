<?php
/**
 * Import live-sourced service copy (v27.3.0) from service-specs.json
 * (build_specs.py). Saves by ACF field KEY. Fields not in the spec keep their
 * current value (e.g. Brand Strategy's process + FAQ stay Phase 2 draft).
 *   STARK_JSON=tools/content-migration/service-specs.json wp eval-file import-service-specs.php
 */
$specs = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
if ( ! $specs ) WP_CLI::error( 'Set STARK_JSON.' );
$k = function ( $n ) { return 'field_stark_svc_' . $n; };

// Copy errors on live, flagged inline so they show on the page until fixed.
$review = array(
    'content-creation' => array( 'final_cta.heading' => '[NEEDS REVIEW — same heading as Paid Advertising on live] ' ),
);

$scalar = array( 'hero_headline', 'hero_thesis', 'hero_note', 'card_description', 'outcomes_eyebrow', 'outcomes_heading', 'outcomes_intro', 'outcomes_note',
    'problem_eyebrow', 'problem_heading', 'problem_body', 'problem_points', 'problem_note', 'included_heading', 'included_intro', 'included_note',
    'process_eyebrow', 'process_heading', 'process_intro', 'process_note', 'fit_eyebrow', 'fit_heading', 'fit_intro', 'fit_good_heading', 'fit_good',
    'fit_bad_heading', 'fit_bad', 'faq_heading', 'faq_intro', 'hero_cta_primary', 'hero_cta_secondary' );
$rows = array( 'outcomes_items', 'included_items', 'process_steps', 'faq_items', 'extra_sections' );

foreach ( $specs as $slug => $spec ) {
    if ( '_' === $slug[0] ) continue;
    $page = get_page_by_path( $slug );
    if ( ! $page ) { WP_CLI::warning( "$slug: no page" ); continue; }
    $id = $page->ID;

    foreach ( $scalar as $f ) {
        if ( array_key_exists( $f, $spec ) ) update_field( $k( $f ), $spec[ $f ], $id );
    }
    foreach ( $rows as $f ) {
        if ( ! array_key_exists( $f, $spec ) ) continue;
        $value = array_map( function ( $row ) {
            return array_filter( $row, function ( $key ) { return '_' !== $key[0]; }, ARRAY_FILTER_USE_KEY );
        }, $spec[ $f ] );
        update_field( $k( $f ), $value, $id );
    }
    if ( isset( $spec['final_cta'] ) ) {
        $cta = $spec['final_cta'];
        if ( isset( $review[ $slug ]['final_cta.heading'] ) ) $cta['heading'] = $review[ $slug ]['final_cta.heading'] . $cta['heading'];
        update_field( $k( 'final_cta' ), array( 'heading' => $cta['heading'], 'subhead' => $cta['subhead'], 'note' => $cta['note'], 'button' => $cta['button'] ?: '' ), $id );
    }
    // Brand Strategy keeps Phase 2 draft process + FAQ (live's are copies of other pages).
    update_field( $k( 'copy_status' ), isset( $spec['_skip'] ) ? 'draft' : 'live', $id );
    update_field( $k( 'hero_eyebrow' ), get_the_title( $id ), $id );
    WP_CLI::log( sprintf( '%-26s → %d  (%s)', $slug, $id, isset( $spec['_skip'] ) ? 'partial — draft kept for process + FAQ' : 'live-sourced' ) );
}
WP_CLI::success( 'Service copy imported.' );
