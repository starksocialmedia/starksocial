<?php
// Register attachments from a WXR export against files already in uploads/.
// Keeps original IDs (import_id) so imported posts' _thumbnail_id resolves.
$xml = simplexml_load_file( getenv( 'STARK_WXR' ) );
$xml->registerXPathNamespace( 'wp', 'http://wordpress.org/export/1.2/' );
$uploads = wp_get_upload_dir();
$done = $skipped = 0;
foreach ( $xml->channel->item as $item ) {
    $wp = $item->children( 'http://wordpress.org/export/1.2/' );
    if ( 'attachment' !== (string) $wp->post_type ) continue;
    $meta = array();
    foreach ( $wp->postmeta as $m ) $meta[ (string) $m->meta_key ] = (string) $m->meta_value;
    $file = $meta['_wp_attached_file'] ?? '';
    $old_id = (int) $wp->post_id;
    if ( ! $file || ! file_exists( $uploads['basedir'] . '/' . $file ) || get_post( $old_id ) ) { $skipped++; continue; }
    $id = wp_insert_post( array(
        'import_id'      => $old_id,
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_title'     => (string) $item->title,
        'post_name'      => (string) $wp->post_name,
        'post_date'      => (string) $wp->post_date,
        'post_mime_type' => wp_check_filetype( $file )['type'],
        'post_parent'    => (int) $wp->post_parent,
        'guid'           => $uploads['baseurl'] . '/' . $file,
        'post_content'   => (string) $item->children( 'http://purl.org/rss/1.0/modules/content/' )->encoded,
        'post_excerpt'   => (string) $item->children( 'http://wordpress.org/export/1.2/excerpt/' )->encoded,
    ), true );
    if ( is_wp_error( $id ) ) { WP_CLI::warning( $id->get_error_message() ); continue; }
    if ( $id !== $old_id ) WP_CLI::warning( "ID $old_id became $id" );
    foreach ( array( '_wp_attached_file', '_wp_attachment_image_alt' ) as $k ) {
        if ( isset( $meta[ $k ] ) ) update_post_meta( $id, $k, $meta[ $k ] );
    }
    if ( isset( $meta['_wp_attachment_metadata'] ) ) update_post_meta( $id, '_wp_attachment_metadata', maybe_unserialize( $meta['_wp_attachment_metadata'] ) );
    $done++;
}
WP_CLI::success( "Registered $done attachments, skipped $skipped." );
