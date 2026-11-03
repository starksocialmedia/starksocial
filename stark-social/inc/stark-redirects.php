<?php
/**
 * Stark 27 legacy redirects (v27.3.16)
 * Replaces two live plugins at launch without carrying them over:
 * - Pretty Links /1 … /37: episode short links printed on the cover art
 *   ("starksocial.com/37" on episode 37). On live, 33 of the 37 point at a
 *   different episode (the links were created out of order), so Phase 2 sends
 *   /N to the episode whose episode_number is N, which is what the covers promise.
 *   Filter 'stark_episode_short_links' (false) restores the plain map behaviour.
 * - AIOSEO Redirects: old blog slugs → current posts
 * Targets were resolved to their final live URL (following live's own
 * redirects) and checked as 200 on Phase 2. Generated from live's tables on
 * November 2 2026; skipped: 1 truncated source, 2 targets already broken on live.
 * Only fires on 404s, so it can never shadow a real page.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_legacy_redirect_map() {
    return apply_filters( 'stark_legacy_redirects', array(
        '/scv-today-may-14-2014'                                                                        => '/engaging-with-your-audience-through-social-media-insights-from-stark-social-media/', // AIOSEO
        '/content-is-king'                                                                              => '/mastering-social-media-why-content-is-king/', // AIOSEO
        '/why-every-small-business-need-a-call-to-action-in-marketing'                                  => '/why-every-small-business-needs-a-call-to-action-in-marketing/', // AIOSEO
        '/understanding-and-implementing-two-factor-authentication-2fa-to-secure-your-online-accounts'  => '/securing-your-online-accounts-with-two-factor-authentication-2fa/', // AIOSEO
        '/should-your-small-business-have-a-blog-understanding-the-role-of-content-marketing'           => '/docs/should-your-small-business-have-a-blog-understanding-the-role-of-content-marketing/', // AIOSEO
        '/the-dos-and-donts-of-social-media-branding'                                                   => '/mastering-social-media-branding-dos-and-donts/', // AIOSEO
        '/public-relations-done-right'                                                                  => '/rockstar-public-relations-taking-your-biz-to-the-next-level/', // AIOSEO
        '/twitter-3-0-webinar'                                                                          => '/mastering-twitter-3-0-advanced-strategies-webinar/', // AIOSEO
        '/infographic-facebook-etiquette-2018'                                                          => '/infographic-mastering-facebook-etiquette-in-2018/', // AIOSEO
        '/services/search-engine-optimization-seo'                                                      => '/seo/', // AIOSEO
        '/password-generator'                                                                           => '/support/password-generator/', // AIOSEO
    ) );
}

function stark_legacy_redirects() {
    if ( ! is_404() ) return;
    $path = '/' . trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' );
    // /N → podcast episode N.
    if ( preg_match( '#^/(\d{1,3})$#', $path, $m ) && apply_filters( 'stark_episode_short_links', true ) ) {
        $episode = get_posts( array( 'post_type' => 'podcast', 'numberposts' => 1, 'meta_key' => 'episode_number', 'meta_value' => $m[1], 'fields' => 'ids' ) );
        if ( $episode ) {
            wp_safe_redirect( get_permalink( $episode[0] ), 301, 'Stark episode short link' );
            exit;
        }
    }
    $map  = stark_legacy_redirect_map();
    if ( isset( $map[ $path ] ) ) {
        wp_safe_redirect( home_url( $map[ $path ] ), 301, 'Stark legacy redirect' );
        exit;
    }
}
add_action( 'template_redirect', 'stark_legacy_redirects', 0 );
