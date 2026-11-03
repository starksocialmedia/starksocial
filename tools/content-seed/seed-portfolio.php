<?php
// Import live `work` items as `portfolio`.
$rows = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );

function stark_seed_sections( $html ) {
    // Live content: <section><h2>Overview|The Challenge|Our Solution|Results…</h2>…</section>
    $out = array();
    preg_match_all( '#<section[^>]*>\s*<h2[^>]*>(.*?)</h2>(.*?)</section>#s', $html, $m, PREG_SET_ORDER );
    foreach ( $m as $sec ) {
        $label = strtolower( wp_strip_all_tags( $sec[1] ) );
        $body  = trim( preg_replace( '/\s*role="list"/', '', $sec[2] ) );
        if ( false !== strpos( $label, 'overview' ) ) $out['overview'] = $body;
        elseif ( false !== strpos( $label, 'challenge' ) ) $out['challenge'] = $body;
        elseif ( false !== strpos( $label, 'solution' ) || false !== strpos( $label, 'approach' ) ) $out['approach'] = $body;
        elseif ( false !== strpos( $label, 'result' ) ) $out['results_summary'] = $body;
    }
    return $out;
}

$extra = array(
    'pathway-escrow' => array( 'industry' => 'Real Estate & Escrow', 'year' => 2026, 'services' => array( 'web-design', 'seo' ), 'hero' => 1775, 'gallery' => array( 1776, 1777, 1780 ) ),
    'schlickart'     => array( 'industry' => 'Photography', 'year' => 2025, 'services' => array( 'web-design', 'brand-strategy-identity' ), 'hero' => 1789, 'gallery' => array( 1788 ) ),
);

foreach ( $rows as $row ) {
    $p = $row['post']; $m = $row['meta']; $x = $extra[ $p['post_name'] ];
    if ( get_page_by_path( $p['post_name'], OBJECT, 'portfolio' ) ) { WP_CLI::log( "skip {$p['post_name']}" ); continue; }
    $id = wp_insert_post( array( 'post_type' => 'portfolio', 'post_status' => 'publish', 'post_title' => $p['post_title'], 'post_name' => $p['post_name'], 'post_date' => $p['post_date'] ), true );
    set_post_thumbnail( $id, $x['hero'] );
    update_field( 'client_name', $m['client_name'], $id );
    update_field( 'industry', $x['industry'], $id );
    update_field( 'project_year', $x['year'], $id );
    update_field( 'services', $x['services'], $id );
    update_field( 'external_url', $m['website_url'], $id );
    update_field( 'summary', $m['short_outcome'], $id );
    foreach ( stark_seed_sections( $p['post_content'] ) as $k => $v ) update_field( $k, $v, $id );
    $results = array();
    for ( $i = 0; $i < 3; $i++ ) {
        if ( ! empty( $m["results_{$i}_value"] ) ) $results[] = array( 'value' => $m["results_{$i}_value"], 'label' => $m["results_{$i}_metric"] );
    }
    update_field( 'results', $results, $id );
    update_field( 'gallery', array_map( function ( $a ) { return array( 'image' => $a, 'caption' => '' ); }, $x['gallery'] ), $id );
    WP_CLI::log( "imported {$p['post_name']} → $id" );
}

// v27.5.1: no Valencia Jazz & Blues case study. It's a 2017 blog post (the award), not portfolio
// work; tools/content-migration/valencia-blog-only.php removes the entry this used to create.
