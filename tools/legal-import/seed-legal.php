<?php
$data = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
$titles = stark_get_legal_pages();
$support = get_page_by_path( 'support' );
$support_id = $support ? $support->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Support', 'post_name' => 'support' ) );
WP_CLI::log( "support → $support_id" );
foreach ( $data as $slug => $d ) {
    $content = '<p>Last updated: ' . esc_html( $d['updated'] ) . "</p>\n" . $d['html'];
    $existing = get_page_by_path( 'support/' . $slug ) ?: get_page_by_path( $slug );
    if ( ! $existing && 'privacy-policy' === $slug ) $existing = get_post( (int) get_option( 'wp_page_for_privacy_policy' ) );
    $args = array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $titles[ $slug ], 'post_name' => $slug, 'post_parent' => $support_id, 'post_content' => wp_slash( $content ) );
    if ( $existing ) { $args['ID'] = $existing->ID; $id = wp_update_post( $args ); } else { $id = wp_insert_post( $args ); }
    WP_CLI::log( "$slug → $id " . get_permalink( $id ) );
}
