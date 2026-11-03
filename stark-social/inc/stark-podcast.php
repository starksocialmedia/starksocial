<?php
/**
 * Stark 27 podcast (v27.0.16) — The Social Life Podcast
 * Archive /podcast/ and episode singles. The `podcast` post type is
 * registered in functions.php (v2.0.0). Episode data comes from Podlove when
 * it is active, otherwise from post meta imported from live
 * (episode_number, episode_duration, episode_subtitle, episode_audio_url,
 * episode_transcript — see tools/podcast-import/).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_podcast_template( $template ) {
    $file = null;
    if ( is_post_type_archive( 'podcast' ) ) {
        $file = 'stark-podcast-archive.php';
    } elseif ( is_singular( 'podcast' ) ) {
        $file = 'stark-podcast-single.php';
    }
    if ( $file && file_exists( get_stylesheet_directory() . '/templates/' . $file ) ) {
        return get_stylesheet_directory() . '/templates/' . $file;
    }
    return $template;
}
add_filter( 'template_include', 'stark_podcast_template', 99 );

function stark_body_class_podcast( $classes ) {
    if ( is_post_type_archive( 'podcast' ) || is_singular( 'podcast' ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = is_singular( 'podcast' ) ? 'stark-podcast-single' : 'stark-podcast-archive';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_podcast' );

function stark_podcast_posts_per_page( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'podcast' ) ) {
        $query->set( 'posts_per_page', 12 );
    }
}
add_action( 'pre_get_posts', 'stark_podcast_posts_per_page' );

/**
 * Show details — mirrors live's Podlove podcast settings.
 */
function stark_podcast_show() {
    return apply_filters( 'stark_podcast_show', array(
        'title'    => 'The Social Life Podcast',
        'subtitle' => 'Presented by Stark Social Media Agency',
        'summary'  => 'Deanna and Nathan talk social media, websites and marketing for small businesses: practical advice, honest opinions and the occasional tangent.',
    ) );
}

/**
 * Where to listen. Feed URL is Podlove's — it must keep working after launch.
 */
function stark_podcast_subscribe_links() {
    return apply_filters( 'stark_podcast_subscribe_links', array(
        array( 'label' => 'Apple Podcasts', 'url' => 'https://podcasts.apple.com/us/podcast/the-social-life-podcast/id1055513296', 'icon' => 'apple' ),
        array( 'label' => 'YouTube', 'url' => 'https://www.youtube.com/c/Starksocial', 'icon' => 'youtube' ),
        array( 'label' => 'RSS feed', 'url' => home_url( '/feed/podcast/' ), 'icon' => 'rss' ),
    ) );
}

function stark_podcast_platform_icon( $name ) {
    $paths = array(
        'apple'   => '<path d="M12 3a6 6 0 0 0-6 6c0 2 1 3.6 2.4 4.7.5.4.6 1 .6 1.6V21h6v-5.7c0-.6.2-1.2.6-1.6A6 6 0 0 0 12 3Z"/><circle cx="12" cy="9" r="2"/>',
        'youtube' => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3Z"/>',
        'rss'     => '<path d="M4 11a9 9 0 0 1 9 9M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/>',
    );
    if ( ! isset( $paths[ $name ] ) ) return '';
    return '<svg class="stark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Episode data for cards and the single.
 */
function stark_podcast_episode( $post = null ) {
    $post = get_post( $post );
    $id   = $post->ID;
    return array(
        'id'         => $id,
        'title'      => get_the_title( $id ),
        'url'        => get_permalink( $id ),
        'number'     => get_post_meta( $id, 'episode_number', true ),
        'duration'   => get_post_meta( $id, 'episode_duration', true ),
        'subtitle'   => get_post_meta( $id, 'episode_subtitle', true ),
        'audio'      => get_post_meta( $id, 'episode_audio_url', true ),
        'transcript' => get_post_meta( $id, 'episode_transcript', true ),
        'meta_line'  => do_shortcode( '[starkpodmeta id="' . $id . '"]' ),
        'excerpt'    => wp_trim_words( get_the_excerpt( $id ), 26, '…' ),
        'cover_id'   => get_post_thumbnail_id( $id ),
    );
}

/**
 * Player: Podlove Web Player when the plugin is active, native audio otherwise.
 */
function stark_podcast_player( $episode ) {
    if ( shortcode_exists( 'podlove-episode-web-player' ) ) {
        return do_shortcode( '[podlove-episode-web-player post_id="' . (int) $episode['id'] . '"]' );
    }
    if ( ! $episode['audio'] ) return '';
    return sprintf(
        '<div class="stark-player"><audio class="stark-player__audio" controls preload="none" src="%1$s"></audio><a class="stark-player__download" href="%1$s" download>Download MP3</a></div>',
        esc_url( $episode['audio'] )
    );
}

/**
 * Related episodes: same category first, then most recent.
 */
function stark_podcast_related( $post_id, $count = 3 ) {
    $cats  = wp_get_post_categories( $post_id );
    $posts = $cats ? get_posts( array( 'post_type' => 'podcast', 'numberposts' => $count, 'category__in' => $cats, 'post__not_in' => array( $post_id ) ) ) : array();
    if ( count( $posts ) < $count ) {
        $posts = array_merge( $posts, get_posts( array(
            'post_type'    => 'podcast',
            'numberposts'  => $count - count( $posts ),
            'post__not_in' => array_merge( array( $post_id ), wp_list_pluck( $posts, 'ID' ) ),
        ) ) );
    }
    return $posts;
}

/**
 * Transcript as paragraphs — the .txt files are one paragraph per line.
 */
function stark_podcast_transcript_html( $text ) {
    $lines = array_filter( array_map( 'trim', preg_split( '/\R/', (string) $text ) ) );
    return implode( "\n", array_map( function ( $line ) {
        return '<p>' . esc_html( $line ) . '</p>';
    }, $lines ) );
}

/**
 * Attachment <img> that also works for SVGs (wp_get_attachment_image()
 * returns '' for them) — most episode cover art on live is SVG.
 */
function stark_attachment_img( $id, $size = 'medium_large', $attr = array() ) {
    if ( ! $id ) return '';
    $html = wp_get_attachment_image( $id, $size, false, $attr );
    if ( $html ) return $html;
    $url = wp_get_attachment_url( $id );
    if ( ! $url ) return '';
    $attr = array_merge( array( 'alt' => get_post_meta( $id, '_wp_attachment_image_alt', true ), 'decoding' => 'async' ), $attr );

    // v27.1.4 — WebP renders of SVG covers (tools/podcast-import/register-webp-covers.php):
    // ~65 KB instead of 100 KB–1.7 MB per cover.
    $webp = get_post_meta( $id, '_stark_webp_sizes', true );
    if ( $webp ) {
        $base   = trailingslashit( dirname( $url ) );
        $srcset = array();
        foreach ( $webp as $s ) $srcset[] = $base . rawurlencode( $s['file'] ) . ' ' . (int) $s['width'] . 'w';
        $attr = array_merge( array(
            'src'    => $base . rawurlencode( $webp[0]['file'] ),
            'srcset' => implode( ', ', $srcset ),
            'sizes'  => '(min-width: 1024px) 420px, 100vw',
            'width'  => (int) $webp[0]['width'],
            'height' => (int) $webp[0]['height'],
        ), $attr );
        $url = $attr['src'];
        unset( $attr['src'] );
    }
    $out  = '<img src="' . esc_url( $url ) . '"';
    foreach ( $attr as $k => $v ) $out .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
    return $out . '>';
}
