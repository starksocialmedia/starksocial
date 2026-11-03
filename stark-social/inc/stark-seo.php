<?php
/**
 * Stark 27 SEO glue for Rank Math (v27.1.5)
 * Site-wide settings live in Rank Math (tools/rankmath/configure.php). This
 * module fills the gaps the templates create: Phase 2 pages keep their copy in
 * ACF, not post content, so Rank Math's %excerpt% comes back empty.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Meta description fallback when Rank Math has none (no per-page value, empty
 * excerpt): the page's own lead copy, trimmed to ~155 characters.
 */
function stark_seo_description_fallback( $description ) {
    if ( '' !== trim( (string) $description ) ) return $description;

    $text = '';
    if ( is_singular( 'portfolio' ) ) {
        $text = stark_field( 'summary', get_queried_object_id() );
    } elseif ( is_page() ) {
        $id = get_queried_object_id();
        foreach ( array( 'card_description', 'hero_thesis', 'hero_intro' ) as $field ) {
            $text = stark_field( $field, $id );
            if ( $text ) break;
        }
        // Location pages (/{service}/{area}/) fall back to their service page.
        if ( ! $text && ( $parent = wp_get_post_parent_id( $id ) ) ) {
            $text = stark_field( 'card_description', $parent ) ?: stark_field( 'hero_thesis', $parent );
        }
    } elseif ( is_home() && function_exists( 'stark_blog_archive_heading' ) ) {
        $text = stark_blog_archive_heading()[2];
    } elseif ( is_post_type_archive( 'podcast' ) && function_exists( 'stark_podcast_show' ) ) {
        $text = stark_podcast_show()['summary'];
    } elseif ( is_author() ) {
        $text = get_the_author_meta( 'description', get_queried_object_id() );
    }

    $text = trim( wp_strip_all_tags( (string) $text ) );
    if ( mb_strlen( $text ) > 160 ) {
        $text = rtrim( mb_substr( $text, 0, 155 ), " ,.;:—–-" ) . '…';
    }
    return $text ?: $description;
}
add_filter( 'rank_math/frontend/description', 'stark_seo_description_fallback', 20 );

/**
 * Location pages (/{service}/{area}/) are titled "Web Design in Antelope Valley, CA"
 * unless a Rank Math title was set on the page.
 */
function stark_seo_location_title( $title ) {
    if ( ! is_page() || ! function_exists( 'stark_get_services' ) || ! function_exists( 'stark_get_markets' ) ) return $title;
    $page   = get_queried_object();
    $parent = $page->post_parent ? get_post( $page->post_parent ) : null;
    $svc    = $parent ? ( stark_get_services()[ $parent->post_name ] ?? null ) : null;
    $market = stark_get_markets()[ $page->post_name ] ?? null;
    if ( ! $svc || ! $market || get_post_meta( $page->ID, 'rank_math_title', true ) ) return $title;
    $market_name = stark_field( 'market_name', $page->ID ) ?: $market['name'];
    return sprintf( '%s in %s, CA | %s', $svc['name'], $market_name, get_bloginfo( 'name' ) );
}
add_filter( 'rank_math/frontend/title', 'stark_seo_location_title', 20 );

/**
 * The five markets as areaServed on the business entity (Rank Math has no
 * setting for it), each linking to its card on /service-areas/. Also drops
 * the default Article entity from pages.
 */
function stark_seo_area_served( $data ) {
    if ( ! function_exists( 'stark_get_markets' ) ) return $data;
    $areas = array();
    foreach ( stark_get_markets() as $slug => $market ) {
        $areas[] = array( '@type' => 'Place', 'name' => $market['name'] . ', CA', 'url' => home_url( '/service-areas/#' . $slug ) );
    }
    foreach ( $data as $key => $entity ) {
        if ( ! is_array( $entity ) || empty( $entity['@type'] ) ) continue;
        $types = (array) $entity['@type'];
        // Pages (home, services, about…) and episodes aren't articles — keep WebPage, drop Article.
        if ( ( is_page() || is_singular( 'podcast' ) ) && array_intersect( $types, array( 'Article', 'BlogPosting', 'NewsArticle' ) ) ) {
            unset( $data[ $key ] );
            continue;
        }
        if ( array_intersect( $types, array( 'ProfessionalService', 'LocalBusiness', 'Organization' ) ) ) {
            $data[ $key ]['areaServed'] = $areas;
        }
    }
    return $data;
}
add_filter( 'rank_math/json_ld', 'stark_seo_area_served', 99 );

/**
 * Episodes: Rank Math adds no default schema for the podcast post type, which
 * also drops the Organization / WebSite / WebPage graph. Keep the graph and
 * add a PodcastEpisode built from the episode meta.
 */
add_filter( 'rank_math/schema/add_global_entities', function ( $add ) {
    return is_singular( 'podcast' ) ? true : $add;
} );
add_filter( 'rank_math/json_ld', function ( $data ) {
    if ( is_singular( 'podcast' ) && function_exists( 'stark_podcast_episode' ) ) {
        $data['stark_episode'] = stark_seo_podcast_episode();
    }
    return $data;
}, 100 );

/**
 * PodcastEpisode for an episode single, from the imported episode meta.
 */
function stark_seo_podcast_episode() {
    $ep   = stark_podcast_episode( get_queried_object() );
    $show = stark_podcast_show();
    $node = array(
        '@type'         => 'PodcastEpisode',
        '@id'           => $ep['url'] . '#episode',
        'url'           => $ep['url'],
        'name'          => $ep['title'],
        'datePublished' => get_the_date( 'c', $ep['id'] ),
        'description'   => $ep['subtitle'] ?: wp_strip_all_tags( get_the_excerpt( $ep['id'] ) ),
        'partOfSeries'  => array( '@type' => 'PodcastSeries', 'name' => $show['title'], 'url' => get_post_type_archive_link( 'podcast' ) ),
    );
    if ( $ep['number'] ) $node['episodeNumber'] = (int) $ep['number'];
    if ( $ep['audio'] ) {
        $node['associatedMedia'] = array( '@type' => 'MediaObject', 'contentUrl' => $ep['audio'] );
        // "41:47" or "1:02:03" → ISO 8601 duration.
        if ( preg_match( '/^(?:(\d+):)?(\d+):(\d{2})$/', (string) $ep['duration'], $d ) ) {
            $node['timeRequired'] = sprintf( 'PT%sM%dS', ( (int) $d[1] * 60 + (int) $d[2] ), (int) $d[3] );
            $node['associatedMedia']['duration'] = $node['timeRequired'];
        }
    }
    if ( $ep['cover_id'] ) $node['image'] = wp_get_attachment_url( $ep['cover_id'] );
    return $node;
}
