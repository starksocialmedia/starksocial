<?php
/**
 * Import live's BetterDocs knowledgebase (docs, categories, settings) from a JSON
 * export. Idempotent by slug. Usage:
 *   STARK_JSON=kb.json wp eval-file seed-kb.php
 * The export command is in README.md.
 */
$data = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
if ( ! $data ) WP_CLI::error( 'Set STARK_JSON to the export file.' );

// Knowledgebase page under /support/ — BetterDocs' built-in docs page.
$support = get_page_by_path( 'support' );
if ( ! $support ) WP_CLI::error( 'Create the Support page first.' );
$kb = get_page_by_path( 'support/knowledgebase' );
$kb_id = $kb ? $kb->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Knowledgebase', 'post_name' => 'knowledgebase', 'post_parent' => $support->ID ) );

// Same settings as live, pointing at this site's page.
$settings = (array) $data['settings'];
$settings['docs_page'] = $kb_id;
update_option( 'betterdocs_settings', $settings );

$term_ids = array();
foreach ( $data['terms'] as $t ) {
    $existing = get_term_by( 'slug', $t['slug'], 'doc_category' );
    $id = $existing ? $existing->term_id : wp_insert_term( $t['name'], 'doc_category', array( 'slug' => $t['slug'], 'description' => $t['description'] ) )['term_id'];
    if ( '' !== $t['order'] ) update_term_meta( $id, 'doc_category_order', $t['order'] );
    $term_ids[ $t['slug'] ] = $id;
}

$n = 0;
foreach ( $data['docs'] as $d ) {
    $existing = get_page_by_path( $d['slug'], OBJECT, 'docs' );
    $id = wp_insert_post( wp_slash( array(
        'ID'                => $existing ? $existing->ID : 0,
        'post_type'         => 'docs',
        'post_status'       => 'publish',
        'post_title'        => $d['title'],
        'post_name'         => $d['slug'],
        'post_content'      => $d['content'],
        'post_excerpt'      => $d['excerpt'],
        'post_date'         => $d['date'],
        'post_modified'     => $d['modified'],
        'menu_order'        => $d['menu_order'],
        'post_author'       => $d['author'],
    ) ), true );
    if ( is_wp_error( $id ) ) { WP_CLI::warning( $d['slug'] . ': ' . $id->get_error_message() ); continue; }
    wp_set_object_terms( $id, array_values( array_intersect_key( $term_ids, array_flip( $d['cats'] ) ) ), 'doc_category' );
    if ( $d['tags'] ) wp_set_object_terms( $id, $d['tags'], 'doc_tag' );
    $n++;
}
flush_rewrite_rules( false );
WP_CLI::success( "$n docs, " . count( $term_ids ) . " categories; knowledgebase page $kb_id" );
