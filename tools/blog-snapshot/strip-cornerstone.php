<?php
/**
 * Strip Pro/X + Cornerstone shortcode wrappers from blog posts (post_type=post only).
 * Keeps inner text/HTML. [cs_component href=… titletag=…] becomes a plain link;
 * other [cs_component] embeds (render nothing without Cornerstone) are removed.
 * Usage: wp eval-file strip-cornerstone.php [apply] [type=podcast]   (dry run by default; post type defaults to post)
 */
$apply = in_array( 'apply', $args, true );
$type  = 'post';
foreach ( $args as $a ) if ( 0 === strpos( $a, 'type=' ) ) $type = substr( $a, 5 );
global $wpdb;

function stark_strip_cs( $html ) {
    // Cornerstone SEO blocks store literal "\n" sequences.
    $html = preg_replace_callback( '/\[cs_content_seo\](.*?)\[\/cs_content_seo\]/s', function ( $m ) {
        return "\n" . str_replace( '\n', "\n", $m[1] ) . "\n";
    }, $html );
    // Component embeds: keep a link if it carried one.
    $html = preg_replace_callback( '/\[cs_component\b([^\]]*)\]/', function ( $m ) {
        $a = shortcode_parse_atts( $m[1] );
        if ( ! empty( $a['href'] ) ) {
            $label = ! empty( $a['titletag'] ) ? $a['titletag'] : $a['href'];
            return '<a href="' . esc_url( $a['href'] ) . '" target="_blank" rel="noopener">' . esc_html( $label ) . '</a>';
        }
        return '';
    }, $html );
    // Open/close wrappers for cs_* and x_* shortcodes; inner content stays.
    $html = preg_replace( '/\[\/?(?:cs|x)_[a-z0-9_]+\b[^\]]*\]/i', '', $html );
    // Tidy: empty centered paragraphs left behind, excess blank lines.
    $html = preg_replace( '/<p[^>]*>\s*<\/p>/', '', $html );
    return trim( preg_replace( "/\n{3,}/", "\n\n", $html ) );
}

$rows = $wpdb->get_results( "SELECT ID, post_name, post_content FROM {$wpdb->posts} WHERE post_type = '" . esc_sql( $type ) . "' AND post_content REGEXP '\\\\[/?(cs|x)_'" );
foreach ( $rows as $row ) {
    $new = stark_strip_cs( $row->post_content );
    if ( $new === $row->post_content ) continue;
    WP_CLI::log( sprintf( '%s %d %s (%d → %d chars)', $apply ? 'UPDATED' : 'WOULD UPDATE', $row->ID, $row->post_name, strlen( $row->post_content ), strlen( $new ) ) );
    if ( preg_match( '/\[\/?(?:cs|x)_/', $new ) ) WP_CLI::warning( "  leftover shortcode in {$row->ID}" );
    if ( $apply ) {
        $wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $row->ID ) );
        clean_post_cache( $row->ID );
    }
}
WP_CLI::success( count( $rows ) . ' candidate posts' . ( $apply ? ' processed.' : ' (dry run).' ) );
