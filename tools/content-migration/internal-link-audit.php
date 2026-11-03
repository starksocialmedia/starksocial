<?php
/**
 * Internal link audit (v27.4.3).
 *
 * Fetches every published page, post, case study, episode and KB article as
 * rendered HTML, and counts the links between them that sit inside <main>.
 * Header and footer navigation link to the same pages from everywhere, so they're
 * left out: this measures contextual links only. Writes a Markdown report.
 *
 *   wp eval-file tools/content-migration/internal-link-audit.php [out.md]
 *
 * Inbound counts are by source page (several links from one page count once).
 */

$out   = $args[0] ?? dirname( __FILE__ ) . '/internal-link-report.md';
$types = array( 'page', 'post', 'portfolio', 'podcast', 'docs' );
$home  = untrailingslashit( home_url() );

$targets = array(); // path => [id, type, title]
foreach ( $types as $type ) {
    foreach ( get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1 ) ) as $p ) {
        $path = wp_make_link_relative( get_permalink( $p ) );
        // noindex utility pages aren't link targets that matter.
        if ( in_array( $p->post_name, array( 'thank-you' ), true ) ) continue;
        $targets[ $path ] = array( 'id' => $p->ID, 'type' => $type, 'title' => html_entity_decode( get_the_title( $p ), ENT_QUOTES ) );
    }
}
// Archives are link sources too (they're how most posts are reached).
$sources = array_keys( $targets );
foreach ( array( '/blog/', '/work/', '/podcast/', '/support/knowledgebase/', '/service-areas/' ) as $a ) $sources[] = $a;
// Paged archives: older posts are only reachable from /blog/page/N/.
foreach ( array( '/blog/', '/podcast/' ) as $a ) {
    for ( $n = 2; $n < 50; $n++ ) {
        $code = wp_remote_retrieve_response_code( wp_remote_head( $home . $a . 'page/' . $n . '/', array( 'sslverify' => false, 'timeout' => 20, 'redirection' => 0 ) ) );
        if ( 200 !== (int) $code ) break;
        $sources[] = $a . 'page/' . $n . '/';
    }
}
$sources = array_unique( $sources );

$inbound = array_fill_keys( array_keys( $targets ), array() );
$outbound = array();

foreach ( $sources as $src ) {
    $res = wp_remote_get( $home . $src, array( 'sslverify' => false, 'timeout' => 30, 'redirection' => 0 ) );
    if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) continue;
    $html = wp_remote_retrieve_body( $res );
    if ( ! preg_match( '#<main\b.*?</main>#s', $html, $m ) ) continue;

    $doc = new DOMDocument();
    libxml_use_internal_errors( true );
    $doc->loadHTML( '<?xml encoding="utf-8"?>' . $m[0] );
    libxml_clear_errors();
    $seen = array();
    foreach ( $doc->getElementsByTagName( 'a' ) as $a ) {
        $href = $a->getAttribute( 'href' );
        if ( ! $href || '#' === $href[0] ) continue;
        $u = wp_parse_url( $href );
        if ( ! empty( $u['host'] ) && wp_parse_url( $home, PHP_URL_HOST ) !== $u['host'] ) continue;
        $path = trailingslashit( $u['path'] ?? '/' );
        if ( $path === $src || ! isset( $targets[ $path ] ) || isset( $seen[ $path ] ) ) continue;
        $seen[ $path ] = true;
        $inbound[ $path ][] = $src;
    }
    $outbound[ $src ] = count( $seen );
}

// Report.
$rows = array();
foreach ( $inbound as $path => $from ) {
    $rows[] = array( 'path' => $path, 'type' => $targets[ $path ]['type'], 'title' => $targets[ $path ]['title'], 'in' => count( $from ), 'from' => $from );
}
usort( $rows, function ( $a, $b ) { return $a['in'] <=> $b['in'] ?: strcmp( $a['path'], $b['path'] ); } );

$orphans = array_filter( $rows, function ( $r ) { return 0 === $r['in']; } );
$under   = array_filter( $rows, function ( $r ) { return $r['in'] > 0 && $r['in'] < 3; } );

$md  = "# Internal link report\n\nGenerated " . wp_date( 'F j Y H:i' ) . " on " . $home . " by `tools/content-migration/internal-link-audit.php`.\n";
$md .= "Counts **contextual** links: links inside `<main>` from other published pages (header and footer nav excluded). Archives (`/blog/` and its pages, `/work/`, `/podcast/` and its pages, KB home, `/service-areas/`) count as sources.\n\n";
$md .= sprintf( "| | Count |\n|---|---|\n| Pages checked | %d |\n| Orphans (0 inbound) | %d |\n| Under-linked (1–2 inbound) | %d |\n| 3+ inbound | %d |\n\n", count( $rows ), count( $orphans ), count( $under ), count( $rows ) - count( $orphans ) - count( $under ) );
foreach ( array( 'Orphans (0 inbound)' => $orphans, 'Under-linked (1–2 inbound)' => $under ) as $h => $set ) {
    $md .= "## $h\n\n| Page | Type | Inbound | From |\n|---|---|---|---|\n";
    foreach ( $set as $r ) {
        $md .= sprintf( "| `%s` %s | %s | %d | %s |\n", $r['path'], str_replace( '|', '\\|', $r['title'] ), $r['type'], $r['in'], implode( ', ', array_map( function ( $f ) { return '`' . $f . '`'; }, $r['from'] ) ) );
    }
    $md .= "\n";
}
$md .= "## All pages by type (inbound contextual links)\n\n";
foreach ( $types as $type ) {
    $set = array_filter( $rows, function ( $r ) use ( $type ) { return $type === $r['type']; } );
    if ( ! $set ) continue;
    $md .= "**$type** — " . implode( ' · ', array_map( function ( $r ) { return '`' . $r['path'] . '` ' . $r['in']; }, $set ) ) . "\n\n";
}
file_put_contents( $out, $md );
WP_CLI::success( sprintf( '%d pages · %d orphans · %d under-linked → %s', count( $rows ), count( $orphans ), count( $under ), $out ) );
