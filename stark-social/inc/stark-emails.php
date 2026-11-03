<?php
/**
 * Stark 27 email templates (v27.3.10; real wordmark in the header since v27.5.0)
 * One branded, div-based HTML wrapper (no tables, no <br> — see ERR-001) for:
 * - WordPress core emails: password reset, new user, comment notification + moderation
 * - Gravity Forms notifications on "Stark Social …" forms
 * Gravity Forms gets a div-based {stark_fields} merge tag instead of the
 * table-based {all_fields}. Staging never sends mail (stark-staging-guard).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Wrap inner HTML in the Stark email shell. Inline styles only.
 */
function stark_email_wrap( $inner, $preheader = '' ) {
    $font  = "font-family:Barlow,-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;";
    $home  = esc_url( home_url( '/' ) );
    $year  = gmdate( 'Y' );
    $pre   = $preheader ? '<div style="display:none;max-height:0;overflow:hidden;opacity:0;">' . esc_html( $preheader ) . '</div>' : '';
    return '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="light only"></head>'
        . '<body style="margin:0;padding:0;background:#F7F8FA;' . $font . '">' . $pre
        . '<div style="max-width:600px;margin:0 auto;padding:32px 16px;">'
        . '<div style="background:#031B34;border-radius:16px 16px 0 0;padding:20px 28px;">'
        // Real wordmark as a 2x PNG (email clients don't render SVG); alt text stays readable when images are blocked (v27.5.0)
        . '<a href="' . $home . '" style="color:#ffffff;text-decoration:none;font-size:20px;font-weight:700;' . $font . '"><img src="' . esc_url( get_stylesheet_directory_uri() . '/img/logos/stark-wordmark-light@2x.png' ) . '" width="104" height="60" alt="Stark Social Media Agency" style="display:block;border:0;width:104px;height:60px;color:#ffffff;font-size:18px;font-weight:700;"></a>'
        . '</div>'
        . '<div style="background:#ffffff;border:1px solid #E6E8EC;border-top:0;border-radius:0 0 16px 16px;padding:28px;color:#0A0F1A;font-size:16px;line-height:1.6;">'
        . $inner
        . '</div>'
        . '<div style="padding:20px 8px;color:#6B7280;font-size:12px;line-height:1.6;text-align:center;">'
        . 'Stark Social Media Agency · P.O. Box 55081, Valencia, CA 91385 · <a href="' . $home . '" style="color:#2A70CC;">starksocial.com</a><div>© ' . $year . '</div>'
        . '</div></div></body></html>';
}

/**
 * Plain-text core email body → simple HTML paragraphs (links made clickable).
 */
function stark_email_text_to_html( $text ) {
    $paras = preg_split( '/\R{2,}/', trim( (string) $text ) );
    $out   = '';
    foreach ( $paras as $p ) {
        $lines = array_map( function ( $line ) { return make_clickable( esc_html( $line ) ); }, preg_split( '/\R/', $p ) );
        $out  .= '<div style="margin:0 0 14px;">' . implode( '</div><div>', $lines ) . '</div>';
    }
    return str_replace( '<a ', '<a style="color:#2A70CC;" ', $out );
}

function stark_email_html_headers( $headers ) {
    $headers = (array) $headers;
    $headers = array_filter( $headers, function ( $h ) { return 0 !== stripos( (string) $h, 'content-type' ); } );
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    return $headers;
}

/* Core: password reset + new user ---------------------------------------- */
foreach ( array( 'retrieve_password_notification_email', 'wp_new_user_notification_email', 'wp_new_user_notification_email_admin' ) as $hook ) {
    add_filter( $hook, function ( $email ) {
        if ( empty( $email['message'] ) || false !== stripos( (string) $email['message'], '<html' ) ) return $email;
        $email['message'] = stark_email_wrap( stark_email_text_to_html( $email['message'] ), $email['subject'] ?? '' );
        $email['headers'] = stark_email_html_headers( $email['headers'] ?? array() );
        return $email;
    }, 20 );
}

/* Core: comment notification + moderation -------------------------------- */
foreach ( array( 'comment_notification_text', 'comment_moderation_text' ) as $hook ) {
    add_filter( $hook, function ( $text ) {
        return stark_email_wrap( stark_email_text_to_html( $text ) );
    }, 20 );
}
foreach ( array( 'comment_notification_headers', 'comment_moderation_headers' ) as $hook ) {
    add_filter( $hook, function ( $headers ) {
        return preg_replace( '/Content-Type:[^\n]*\n?/i', '', (string) $headers ) . "Content-Type: text/html; charset=UTF-8\n";
    }, 20 );
}

/* Gravity Forms ---------------------------------------------------------- */

/**
 * {stark_fields}: every non-empty, non-hidden field as label/value pairs, div-based.
 */
add_filter( 'gform_replace_merge_tags', function ( $text, $form, $entry ) {
    if ( false === strpos( $text, '{stark_fields}' ) || ! $entry ) return $text;
    $rows = '';
    foreach ( $form['fields'] as $field ) {
        if ( in_array( $field->type, array( 'hidden', 'html', 'section', 'page', 'captcha' ), true ) ) continue;
        $value = $field->get_value_export( $entry, $field->id, true );
        if ( '' === trim( (string) $value ) ) continue;
        $rows .= '<div style="padding:12px 0;border-top:1px solid #E6E8EC;">'
            . '<div style="font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#6B7280;">' . esc_html( $field->label ) . '</div>'
            . '<div style="margin-top:2px;white-space:pre-line;">' . esc_html( $value ) . '</div></div>';
    }
    return str_replace( '{stark_fields}', '<div style="margin:16px 0;">' . $rows . '</div>', $text );
}, 10, 3 );

/**
 * Wrap notifications of the Stark forms (title starts with "Stark Social").
 */
add_filter( 'gform_notification', function ( $notification, $form ) {
    if ( 0 !== strpos( (string) $form['title'], 'Stark Social' ) ) return $notification;
    $notification['message_format'] = 'html';
    $notification['disableAutoformat'] = true;
    $notification['message'] = stark_email_wrap( $notification['message'], $notification['subject'] ?? '' );
    return $notification;
}, 10, 2 );
