<?php
// Re-save top-level ACF values by field key for posts whose values were stored raw.
foreach ( array_map( 'intval', explode( ',', getenv( 'STARK_IDS' ) ) ) as $id ) {
    $fixed = array();
    foreach ( acf_get_field_groups( array( 'post_id' => $id ) ) as $group ) {
        foreach ( acf_get_fields( $group ) as $f ) {
            if ( ! $f['name'] ) continue;
            $raw = get_post_meta( $id, $f['name'], true );
            if ( '' === $raw || null === $raw ) continue;
            if ( get_post_meta( $id, '_' . $f['name'], true ) === $f['key'] && ! is_array( $raw ) ) continue;
            delete_post_meta( $id, $f['name'] );
            update_field( $f['key'], $raw, $id );
            $fixed[] = $f['name'];
        }
    }
    WP_CLI::log( "$id: " . ( $fixed ? implode( ', ', $fixed ) : 'ok' ) );
}
