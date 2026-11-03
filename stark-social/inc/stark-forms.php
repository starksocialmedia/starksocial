<?php
/**
 * Stark 27 form glue (v27.3.2)
 * Records each Perfex webhook call on the Gravity Forms entry, so "did the
 * lead reach the hub?" is answered in the entry's Notes instead of server logs.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'gform_webhooks_post_request', function ( $response, $feed, $entry, $form ) {
    if ( ! class_exists( 'GFAPI' ) || empty( $entry['id'] ) ) return;
    if ( is_wp_error( $response ) ) {
        $note = 'Webhook "' . ( $feed['meta']['feedName'] ?? '' ) . '" failed: ' . $response->get_error_message();
    } else {
        $code = wp_remote_retrieve_response_code( $response );
        $body = substr( wp_strip_all_tags( wp_remote_retrieve_body( $response ) ), 0, 200 );
        $note = sprintf( 'Webhook "%s" → HTTP %s%s', $feed['meta']['feedName'] ?? '', $code, $body ? ': ' . $body : '' );
    }
    GFAPI::add_note( $entry['id'], 0, 'Stark webhook log', $note, 'stark-webhook', is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) >= 300 ? 'error' : 'success' );
}, 10, 4 );

/**
 * BetterDocs "Was this helpful?" emoji links have no accessible name (axe link-name).
 */
add_action( 'wp_footer', function () {
    if ( ! is_singular( 'docs' ) ) return;
    ?>
<script>document.querySelectorAll('.betterdocs-emoji[data-feelings]').forEach(function(a){var m={happy:'Yes, this was helpful',normal:'It was okay',sad:'No, this wasn’t helpful'};a.setAttribute('aria-label',m[a.dataset.feelings]||a.dataset.feelings);});</script>
    <?php
}, 99 );
