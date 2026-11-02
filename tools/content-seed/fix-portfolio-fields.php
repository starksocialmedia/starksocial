<?php
// Re-save portfolio fields by field KEY so ACF stores proper repeater/group rows.
$group = json_decode( file_get_contents( getenv( 'STARK_GROUP' ) ), true );
$keys  = array();
foreach ( $group['fields'] as $f ) if ( $f['name'] ) $keys[ $f['name'] ] = $f['key'];
foreach ( get_posts( array( 'post_type' => 'portfolio', 'numberposts' => -1, 'post_status' => 'any' ) ) as $p ) {
    foreach ( $keys as $name => $key ) {
        $raw = get_post_meta( $p->ID, $name, true );
        if ( '' === $raw || null === $raw ) continue;
        if ( is_array( $raw ) || ! get_post_meta( $p->ID, '_' . $name, true ) ) {
            delete_post_meta( $p->ID, $name );
            update_field( $key, $raw, $p->ID );
        }
    }
    $g = get_field( 'gallery', $p->ID );
    WP_CLI::log( $p->post_name . ': gallery ' . ( $g ? gettype( $g[0]['image'] ) . ' x' . count( $g ) : 'none' ) . ', results ' . count( (array) get_field( 'results', $p->ID ) ) . ', testimonial ' . ( get_field( 'testimonial', $p->ID )['quote'] ?? '' ? 'yes' : 'no' ) . ', services ' . implode( ',', (array) get_field( 'services', $p->ID ) ) );
}
