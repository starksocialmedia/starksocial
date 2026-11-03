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

/**
 * Perfex PRChat behind cookie consent (v27.3.4). Complianz blocks any script
 * that references hub.starksocial.com/prchat until the visitor accepts the
 * "preferences" category, then runs it. ("functional" can't be used: Complianz
 * always allows it, so the widget's third-party cookies would still load.)
 */
add_filter( 'cmplz_known_script_tags', function ( $tags ) {
    $tags[] = array(
        'name'               => 'perfex-chat',
        'placeholder'        => '',
        'enable_placeholder' => 0,
        'category'           => 'preferences',
        'urls'               => array( 'hub.starksocial.com/prchat' ),
    );
    return $tags;
} );

/* -------------------------------------------------------------------------
 * Contact form anti-spam (v27.3.19) — ported from live's WPCode snippet
 * "Form Validation" (1807), retargeted from live's field IDs (28/29/38) to the
 * Phase 2 form's Message (7) and Email (3). Same rules: no URLs or email
 * addresses in the message, English letters/numbers/punctuation only, and
 * the banned word / email lists hosted on the hub (cached 6 hours, fetched
 * only on contact page renders and form submissions).
 * ------------------------------------------------------------------------- */
function stark_form_spam_list( $url, $key ) {
    $cached = get_transient( $key );
    if ( is_array( $cached ) ) return $cached;
    $resp = wp_remote_get( $url, array( 'timeout' => 6, 'headers' => array( 'Accept' => 'text/plain' ) ) );
    if ( is_wp_error( $resp ) || 200 !== (int) wp_remote_retrieve_response_code( $resp ) ) return array();
    $list = array();
    foreach ( preg_split( '/\R/', (string) wp_remote_retrieve_body( $resp ) ) as $line ) {
        $line = strtolower( trim( $line ) );
        if ( '' !== $line && 0 !== strpos( $line, '#' ) && 0 !== strpos( $line, '//' ) ) $list[] = $line;
    }
    set_transient( $key, $list, 6 * HOUR_IN_SECONDS );
    return $list;
}

function stark_form_is_contact( $form ) {
    return 0 === strpos( (string) ( $form['title'] ?? '' ), 'Stark Social Contact' );
}

add_filter( 'gform_field_validation', function ( $result, $value, $form, $field ) {
    if ( ! $result['is_valid'] || ! stark_form_is_contact( $form ) ) return $result;
    $value = is_string( $value ) ? $value : '';

    if ( 7 === (int) $field->id && '' !== trim( $value ) ) {
        $fail = '';
        if ( preg_match( '/\b((?:https?|ftp):\/\/|www\.)\S+/i', $value ) || preg_match( '/\b[a-z0-9-]+\.(?:[a-z]{2,})(?:\/\S*)?\b/i', $value ) ) {
            $fail = 'Please leave out website addresses — we’ll ask for links on our call.';
        } elseif ( preg_match( '/\b[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}\b/i', $value ) ) {
            $fail = 'Please leave out email addresses — use the Email field above.';
        } elseif ( 1 !== preg_match( '/^[A-Za-z0-9\s\p{P}\p{S}]+$/u', $value ) ) {
            $fail = 'Please use English letters, numbers, spaces and punctuation.';
        } else {
            $hay = strtolower( $value );
            foreach ( stark_form_spam_list( 'https://hub.starksocial.com/media/banned_words.txt', 'stark_gf_banned_words_v1' ) as $w ) {
                if ( false !== strpos( $hay, $w ) ) { $fail = 'Your message contains words we can’t accept.'; break; }
            }
        }
        if ( $fail ) { $result['is_valid'] = false; $result['message'] = $fail; }
    }

    if ( 3 === (int) $field->id && '' !== trim( $value ) ) {
        if ( in_array( strtolower( trim( $value ) ), stark_form_spam_list( 'https://hub.starksocial.com/media/banned_emails.txt', 'stark_gf_banned_emails_v1' ), true ) ) {
            $result['is_valid'] = false;
            $result['message']  = 'This email address can’t be used.';
        }
    }
    return $result;
}, 20, 4 );
