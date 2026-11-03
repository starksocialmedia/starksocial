<?php
/**
 * Per-page Rank Math meta from live's AIOSEO (v27.4.2).
 *
 * Source: live-aioseo-meta.json (exported read-only from live's aioseo_posts,
 * November 3 2026). Live's /services/{slug}/ pages map to Phase 2's /{slug}/.
 * Sets, per page: SEO title, meta description, focus keyword(s) and the share
 * image (live's per-page image, when the file is in the media library). Copy is
 * verbatim; descriptions over 160 characters are reported, not rewritten.
 * Service pages also get a regional secondary keyword; Phase 2-only pages get
 * keywords from SEO-STRATEGY.md's markets. Idempotent.
 *
 *   wp eval-file tools/rankmath/import-live-meta.php [dry]
 */

$dry  = in_array( 'dry', $args ?? array(), true );
$live = json_decode( file_get_contents( __DIR__ . '/live-aioseo-meta.json' ), true );
if ( ! $live ) WP_CLI::error( 'live-aioseo-meta.json missing or invalid' );

// Live pages with no Phase 2 page of their own (author archives, Phase 1 utility pages).
$skip = array( 'search-results', 'stark-404', 'nathan-imhoff', 'deanna-l-miller' );

function stark_rm_target( $slug, $row ) {
    if ( 'home' === $slug ) return (int) get_option( 'page_on_front' );
    if ( 'blog' === $slug ) return (int) get_option( 'page_for_posts' );
    if ( 'work' === $row['type'] ) {
        $p = get_page_by_path( $slug, OBJECT, 'portfolio' );
        return $p ? $p->ID : 0;
    }
    $path = trim( preg_replace( '#^/services/(?=[a-z])#', '/', $row['path'] ), '/' );
    $p = get_page_by_path( $path );
    return $p ? $p->ID : 0;
}

/**
 * Live's share images were copied with the uploads but never registered in the
 * media library (they sit in uploads/ without a year folder). Register one.
 */
function stark_rm_register_upload( $url ) {
    $uploads = wp_get_upload_dir();
    $rel     = ltrim( substr( wp_make_link_relative( $url ), strlen( wp_make_link_relative( $uploads['baseurl'] ) ) ), '/' );
    $file    = $uploads['basedir'] . '/' . $rel;
    if ( ! $rel || ! is_file( $file ) ) return 0;
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $type = wp_check_filetype( $file );
    $att  = wp_insert_attachment( array(
        'post_mime_type' => $type['type'],
        'post_title'     => ucwords( str_replace( array( '-', '_' ), ' ', pathinfo( $file, PATHINFO_FILENAME ) ) ),
        'post_status'    => 'inherit',
    ), $file );
    if ( ! $att || is_wp_error( $att ) ) return 0;
    update_post_meta( $att, '_wp_attached_file', $rel );
    wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $file ) );
    return $att;
}

$report = array();
$set = function ( $id, $key, $value ) use ( $dry ) {
    if ( '' === $value || null === $value ) return;
    if ( ! $dry ) update_post_meta( $id, $key, $value );
};

foreach ( $live as $slug => $row ) {
    if ( in_array( $slug, $skip, true ) ) continue;
    $id = stark_rm_target( $slug, $row );
    if ( ! $id ) { $report[] = array( $slug, '—', 'no Phase 2 page', '', '' ); continue; }

    $title = $row['title'] && 'None' !== $row['title']
        ? str_replace( '#separator_sa', '%sep%', html_entity_decode( $row['title'], ENT_QUOTES ) ) : '';
    $desc  = html_entity_decode( (string) $row['description'], ENT_QUOTES );
    $kw    = array_filter( array_merge( array( $row['focus'] ), $row['additional'] ) );

    // Service pages: add the regional variant as a secondary keyword.
    $post = get_post( $id );
    if ( function_exists( 'stark_is_service_page' ) && stark_is_service_page( $post ) && $kw ) {
        $kw[] = strtolower( preg_replace( '/\s+services?$/i', '', $kw[0] ) ) . ' southern california';
    }

    $set( $id, 'rank_math_title', $title );
    $set( $id, 'rank_math_description', $desc );
    $set( $id, 'rank_math_focus_keyword', implode( ',', array_unique( $kw ) ) );

    $img_note = '';
    $img = $row['og_image'] ?: $row['featured'];
    if ( $img ) {
        $local = home_url( wp_make_link_relative( $img ) );
        $att   = attachment_url_to_postid( $local );
        if ( ! $att && ! $dry ) $att = stark_rm_register_upload( $local );
        if ( $att ) {
            $set( $id, 'rank_math_facebook_image', wp_get_attachment_url( $att ) );
            $set( $id, 'rank_math_facebook_image_id', $att );
            $set( $id, 'rank_math_twitter_use_facebook', 'on' );
            $img_note = basename( $img );
        } else {
            $img_note = 'not in media library: ' . basename( $img );
        }
    }
    $len = mb_strlen( $desc );
    $report[] = array( $slug, get_permalink( $id ), implode( ', ', array_unique( $kw ) ), $len . ( $len > 160 ? ' (long)' : '' ), $img_note );
}

// Phase 2 pages with no live equivalent. Keywords only; descriptions fall back to
// on-page copy (inc/stark-seo.php) until Nathan writes them.
$extra = array(
    'audit-consulting'            => 'digital marketing audit,marketing audit southern california',
    'service-areas'               => 'digital marketing agency southern california,santa clarita marketing agency',
    'web-design/antelope-valley'  => 'web design antelope valley,antelope valley web design',
    'work'                        => 'marketing case studies',
);
foreach ( $extra as $path => $kw ) {
    $p = get_page_by_path( $path );
    if ( ! $p ) { $report[] = array( $path, '—', 'no page', '', '' ); continue; }
    $set( $p->ID, 'rank_math_focus_keyword', $kw );
    $report[] = array( $path, get_permalink( $p ), str_replace( ',', ', ', $kw ), 'fallback', '' );
}
$valencia = get_page_by_path( 'valencia-jazz-blues', OBJECT, 'portfolio' );
if ( $valencia ) {
    $set( $valencia->ID, 'rank_math_focus_keyword', 'valencia jazz & blues,jazz festival website' );
    $report[] = array( 'valencia-jazz-blues', get_permalink( $valencia ), 'valencia jazz & blues, jazz festival website', 'fallback', '' );
}

WP_CLI\Utils\format_items( 'table', array_map( function ( $r ) {
    return array_combine( array( 'live', 'phase2', 'keywords', 'desc_len', 'share_image' ), $r );
}, $report ), array( 'live', 'phase2', 'keywords', 'desc_len', 'share_image' ) );
WP_CLI::success( $dry ? 'Dry run, nothing saved' : 'Rank Math meta imported' );
