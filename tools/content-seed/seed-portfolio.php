<?php
// Import live `work` items as `portfolio` + create Valencia Jazz & Blues draft.
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

// Third case study — facts from the June 2017 award post (ID 498). Narrative is draft.
if ( ! get_page_by_path( 'valencia-jazz-blues', OBJECT, 'portfolio' ) ) {
    $id = wp_insert_post( array( 'post_type' => 'portfolio', 'post_status' => 'publish', 'post_title' => 'Valencia Jazz & Blues', 'post_name' => 'valencia-jazz-blues', 'post_date' => '2017-06-26 09:00:00' ), true );
    set_post_thumbnail( $id, 500 );
    update_field( 'client_name', 'Valencia Jazz & Blues', $id );
    update_field( 'industry', 'Events & Entertainment', $id );
    update_field( 'project_year', 2016, $id );
    update_field( 'services', array( 'web-design' ), $id );
    update_field( 'external_url', 'https://valenciajazzandblues.com', $id );
    update_field( 'summary', 'An award-winning website for the Valencia Jazz & Blues Summer Concert Series.', $id );
    update_field( 'overview', '<p>[DRAFT - needs Nathan review] The Valencia Jazz &amp; Blues Summer Concert Series brings live jazz and blues to Santa Clarita every summer. Stark Social designed the series website, valenciajazzandblues.com, and the work went on to win at the 23rd Annual Communicator Awards, judged by the Academy of Interactive and Visual Arts from more than 6,000 entries.</p>', $id );
    update_field( 'challenge', '<p>[DRAFT - needs Nathan review] A summer concert series lives and dies by its schedule. The site needed to make each season\'s lineup, dates and venue details easy to find, and give the series a look that matched the music.</p>', $id );
    update_field( 'approach', '<p>[DRAFT - needs Nathan review] Nathan led design and build, working directly with the series owner and producer. Add specifics: lineup and ticketing structure, visual direction, and what changed season to season.</p>', $id );
    update_field( 'results_summary', '<p>[DRAFT - needs Nathan review] The site was recognized at the 23rd Annual Communicator Awards in 2017, competing against more than 6,000 entries from agencies and companies across the U.S. and around the world.</p>', $id );
    update_field( 'results', array(
        array( 'value' => 'Winner', 'label' => '23rd Annual Communicator Awards' ),
        array( 'value' => '6,000+', 'label' => 'Entries in the competition' ),
    ), $id );
    update_field( 'testimonial', array(
        'quote' => 'Nathan designed my amazing website, valenciajazzandblues.com. If you need a website for your event or business, there\'s no doubt you need to talk to this extremely creative and talented designer.',
        'name'  => 'Barbara Myler',
        'role'  => 'Owner and Producer, Valencia Jazz & Blues Summer Concert Series',
    ), $id );
    WP_CLI::log( "created valencia-jazz-blues → $id" );
}
