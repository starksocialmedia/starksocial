<?php
/**
 * Stark 27 topics: which services a post is about (v27.4.3 / v27.4.5)
 *
 * One matcher drives three internal-linking features:
 * - "Related services" on blog posts (templates/parts/related-services.php)
 * - "From the blog" on service pages (posts most about that service)
 * - the first mention of a service in a post's text links to the service page
 * Matching is by phrase, title matches weigh triple, and a service needs a
 * score of 2 to count, so a passing mention doesn't make a post "about" it.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Phrases per service. 'link' phrases are also used for in-text links, so
 * they're the unambiguous ones; 'match' adds looser signals for scoring.
 */
function stark_topic_phrases() {
    return apply_filters( 'stark_topic_phrases', array(
        'social-media-management' => array(
            'link'  => array( 'social media management', 'social media manager' ),
            'match' => array( 'social media strateg', 'engagement', 'instagram', 'facebook page', 'linkedin', 'content calendar' ),
        ),
        'paid-advertising' => array(
            'link'  => array( 'paid advertising', 'paid ads', 'paid social' ),
            'match' => array( 'google ads', 'meta ads', 'facebook ads', 'lead generation ads', 'ad spend', 'ppc', 'boosted post' ),
        ),
        'web-design' => array(
            'link'  => array( 'web design', 'website design' ),
            'match' => array( 'website', 'landing page', 'wordpress', 'web designer', 'homepage' ),
        ),
        'seo' => array(
            'link'  => array( 'search engine optimization', 'SEO' ),
            'match' => array( 'google business profile', 'keyword', 'search console', 'rankings', 'local search' ),
        ),
        'content-creation' => array(
            'link'  => array( 'content creation' ),
            'match' => array( 'content marketing', 'video content', 'storytelling', 'blog post', 'content system', 'photography' ),
        ),
        'brand-strategy-identity' => array(
            'link'  => array( 'brand strategy', 'brand identity' ),
            'match' => array( 'branding', 'personal brand', 'style guide', 'brand voice', 'logo' ),
        ),
        'audit-consulting' => array(
            'link'  => array( 'marketing audit' ),
            'match' => array( 'audit', 'checkup', 'consulting' ),
        ),
    ) );
}

function stark_topic_regex( $phrase ) {
    // "SEO" is matched case-sensitively so "seo" in slugs or URLs doesn't count.
    $flags = ( strtoupper( $phrase ) === $phrase ) ? 'u' : 'iu';
    return '/(?<![\w-])' . preg_quote( $phrase, '/' ) . '(?![\w-])/' . $flags;
}

/**
 * Service slugs a post is about, best first: slug => score.
 */
function stark_post_service_scores( $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post ) return array();
    $text  = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
    $title = get_the_title( $post );
    $scores = array();
    foreach ( stark_topic_phrases() as $slug => $set ) {
        $score = 0;
        foreach ( $set['link'] as $p ) {
            $score += 2 * min( 3, preg_match_all( stark_topic_regex( $p ), $text ) ) + 6 * preg_match( stark_topic_regex( $p ), $title );
        }
        foreach ( $set['match'] as $p ) {
            $score += min( 2, preg_match_all( stark_topic_regex( $p ), $text ) ) + 3 * preg_match( stark_topic_regex( $p ), $title );
        }
        if ( $score >= 2 ) $scores[ $slug ] = $score;
    }
    arsort( $scores );
    return $scores;
}

/**
 * Up to $count services for a post, as [slug, name, desc, url] for published service pages.
 */
function stark_post_related_services( $post_id, $count = 3 ) {
    $out = array();
    foreach ( stark_post_service_scores( $post_id ) as $slug => $score ) {
        if ( $score < 3 ) break; // a passing mention isn't enough for the visible row
        $page = get_page_by_path( $slug );
        if ( ! $page || 'publish' !== $page->post_status ) continue;
        $svc   = stark_get_services()[ $slug ];
        $out[] = array(
            'slug' => $slug,
            'name' => $svc['name'],
            'desc' => stark_field( 'card_description', $page->ID ) ?: $svc['desc'],
            'url'  => get_permalink( $page ),
        );
        if ( count( $out ) >= $count ) break;
    }
    return $out;
}

/**
 * Posts most about a service (score, then newest), for its "From the blog"
 * section. Cached until a post changes.
 */
function stark_service_related_posts( $slug, $count = 3 ) {
    $key = 'stark_topic_index_' . md5( (string) get_lastpostmodified( 'gmt', 'post' ) );
    $index = get_transient( $key );
    if ( false === $index ) {
        $index = array();
        foreach ( get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) ) as $id ) {
            foreach ( stark_post_service_scores( $id ) as $s => $score ) $index[ $s ][ $id ] = $score;
        }
        set_transient( $key, $index, WEEK_IN_SECONDS );
    }
    $ids = $index[ $slug ] ?? array();
    uksort( $ids, function ( $a, $b ) use ( $ids ) {
        return $ids[ $b ] <=> $ids[ $a ] ?: strcmp( get_post_field( 'post_date', $b ), get_post_field( 'post_date', $a ) );
    } );
    $ids = array_slice( array_keys( $ids ), 0, $count );
    return $ids ? get_posts( array( 'post__in' => $ids, 'orderby' => 'post__in', 'numberposts' => $count ) ) : array();
}

/**
 * Link the first mention of $phrase inside a <p> or <li> (not inside an
 * existing link). Returns the content; $done is set when a link was added.
 */
function stark_link_first_mention( $content, $phrase, $url, &$done ) {
    $re = stark_topic_regex( $phrase );
    $done = false;
    return preg_replace_callback( '#<(p|li)\b[^>]*>.*?</\1>#s', function ( $block ) use ( $re, $url, &$done ) {
        // One automatic link per paragraph, so two never sit side by side.
        if ( $done || false !== strpos( $block[0], 'stark-topic-link' ) ) return $block[0];
        $parts = preg_split( '#(<a\b.*?</a>|<[^>]+>)#s', $block[0], -1, PREG_SPLIT_DELIM_CAPTURE );
        foreach ( $parts as $i => $part ) {
            if ( '' === $part || '<' === $part[0] ) continue;
            $new = preg_replace( $re, '<a href="' . esc_url( $url ) . '" class="stark-topic-link">$0</a>', $part, 1, $n );
            if ( $n ) { $parts[ $i ] = $new; $done = true; break; }
        }
        return implode( '', $parts );
    }, $content );
}

function stark_content_links_to( $content, $url ) {
    return false !== strpos( $content, 'href="' . $url ) || false !== strpos( $content, 'href="' . wp_make_link_relative( $url ) );
}

/**
 * In-text links on blog posts: the first mention of up to 2 services the post
 * is actually about (score ≥ 4) links to the service page, and the first
 * mention of one of the 5 markets links to its entry on /service-areas/.
 * Only inside paragraphs and list items, never inside an existing link or a
 * heading; skipped when the post already links there. Content in the database
 * is unchanged.
 */
function stark_link_service_mentions( $content ) {
    if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $content;
    $added = 0;
    foreach ( stark_post_service_scores( get_the_ID() ) as $slug => $score ) {
        if ( $added >= 2 || $score < 4 ) break;
        $page = get_page_by_path( $slug );
        if ( ! $page || 'publish' !== $page->post_status || stark_content_links_to( $content, get_permalink( $page ) ) ) continue;
        foreach ( stark_topic_phrases()[ $slug ]['link'] as $phrase ) {
            $content = stark_link_first_mention( $content, $phrase, get_permalink( $page ), $done );
            if ( $done ) { $added++; break; }
        }
    }

    $areas = get_page_by_path( 'service-areas' );
    if ( $areas && 'publish' === $areas->post_status && function_exists( 'stark_get_markets' ) && ! stark_content_links_to( $content, get_permalink( $areas ) ) ) {
        foreach ( stark_get_markets() as $slug => $market ) {
            $content = stark_link_first_mention( $content, $market['name'], get_permalink( $areas ) . '#' . $slug, $done );
            if ( $done ) break;
        }
    }
    return $content;
}
add_filter( 'the_content', 'stark_link_service_mentions', 15 );
