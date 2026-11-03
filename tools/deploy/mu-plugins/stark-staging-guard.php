<?php
/**
 * Plugin Name: Stark staging guard
 * Description: On staging only, stops all outgoing email and keeps search engines out. Does nothing on any other host, so it is safe if it is ever copied to live by mistake.
 * Version: 27.1.0
 *
 * Install: wp-content/mu-plugins/stark-staging-guard.php (see tools/deploy/README.md).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_is_staging() {
    $host = wp_parse_url( home_url(), PHP_URL_HOST );
    return (bool) apply_filters( 'stark_is_staging', 0 === strpos( (string) $host, 'staging.' ) );
}

if ( stark_is_staging() ) {
    // Short-circuit wp_mail() (WP 5.7+): nothing leaves the server. Logged so form tests can be checked.
    add_filter( 'pre_wp_mail', function ( $return, $atts ) {
        $to = is_array( $atts['to'] ) ? implode( ', ', $atts['to'] ) : $atts['to'];
        error_log( sprintf( '[stark-staging-guard] blocked email to %s: %s', $to, $atts['subject'] ) );
        return false;
    }, 1, 2 );

    // Gravity Forms notifications go through wp_mail() too, but stop them at the source as well.
    add_filter( 'gform_disable_notification', '__return_true' );

    // Never indexable, whatever blog_public says.
    add_filter( 'wp_robots', 'wp_robots_no_robots' );
    add_filter( 'rank_math/frontend/robots', function ( $robots ) {
        $robots['index']  = 'noindex';
        $robots['follow'] = 'nofollow';
        return $robots;
    } );
    add_filter( 'robots_txt', function () {
        return "User-agent: *\nDisallow: /\n";
    }, 99 );
}
