<?php
// Restore Podlove options (raw serialized values) from a JSON export. STARK_JSON=podlove-options.json wp eval-file import-options.phpglobal $wpdb;
$o = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
foreach ( $o as $name => $value ) {
    $wpdb->replace( $wpdb->options, array( 'option_name' => $name, 'option_value' => $value, 'autoload' => 'yes' ) );
}
wp_cache_flush();
WP_CLI::success( count( $o ) . ' podlove options' );
