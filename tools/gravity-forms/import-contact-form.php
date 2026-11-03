<?php
/**
 * Create or update the Stark Social contact form (v27.3.2) from
 * stark-contact-form.json, plus its Perfex webhook feed and the /thank-you/ page.
 *
 *   STARK_JSON=tools/gravity-forms/stark-contact-form.json \
 *   STARK_WEBHOOK_SECRET=… wp eval-file tools/gravity-forms/import-contact-form.php
 *
 * The X-Webhook-Secret value is never stored in the repo: pass it in the
 * environment (it's on live's existing "Form to Stark Hub" feed). Without it,
 * the feed is created inactive.
 * Requires Gravity Forms + the Webhooks add-on.
 */
if ( ! class_exists( 'GFAPI' ) || ! class_exists( 'GF_Webhooks' ) ) WP_CLI::error( 'Gravity Forms and the Webhooks add-on must be active.' );
$data = json_decode( file_get_contents( getenv( 'STARK_JSON' ) ), true );
if ( ! $data ) WP_CLI::error( 'Set STARK_JSON.' );

// Thank-you page (rendered by templates/stark-thank-you.php).
$thanks = get_page_by_path( 'thank-you' );
$thanks_id = $thanks ? $thanks->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Thank You', 'post_name' => 'thank-you' ) );
update_post_meta( $thanks_id, 'rank_math_robots', array( 'noindex', 'nofollow' ) );

$form = $data['form'];
foreach ( $form['confirmations'] as &$c ) {
    if ( 'page' === $c['type'] ) { $c['pageId'] = $thanks_id; unset( $c['pageSlug'] ); }
}
unset( $c );

// Form ID 1 if it's free or already ours; otherwise a new ID (the Contact page's Form tab stores it).
$existing = GFAPI::get_form( 1 );
if ( $existing && 'Stark Social Contact' === $existing['title'] ) {
    $form['id'] = 1;
    $result = GFAPI::update_form( $form );
    $form_id = 1;
} else {
    $form_id = GFAPI::add_form( $form );
}
if ( is_wp_error( $form_id ) ) WP_CLI::error( $form_id->get_error_message() );
GFAPI::update_form_property( $form_id, 'is_active', 1 );

$contact = get_page_by_path( 'contact' );
if ( $contact ) update_field( 'field_stark_contact_gf_form_id', $form_id, $contact->ID );

// Webhook feed.
$secret = (string) getenv( 'STARK_WEBHOOK_SECRET' );
$meta   = $data['webhook_feed'];
foreach ( $meta['requestHeaders'] as &$h ) {
    $h['custom_value'] = str_replace( '{{STARK_WEBHOOK_SECRET}}', $secret, $h['custom_value'] );
}
unset( $h );
$old_feeds = GFAPI::get_feeds( null, $form_id, 'gravityformswebhooks' );
foreach ( is_wp_error( $old_feeds ) ? array() : $old_feeds as $old ) {
    GFAPI::delete_feed( $old['id'] );
}
$feed_id = GFAPI::add_feed( $form_id, $meta, 'gravityformswebhooks' );
if ( ! $secret ) GFAPI::update_feed_property( $feed_id, 'is_active', 0 );

WP_CLI::success( sprintf( 'Form %d, webhook feed %d (%s), thank-you page %d', $form_id, $feed_id, $secret ? 'active' : 'INACTIVE — no secret given', $thanks_id ) );
