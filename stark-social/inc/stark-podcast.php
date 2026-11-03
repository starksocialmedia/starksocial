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
    $native = $episode['audio'] ? sprintf(
        '<audio class="stark-player__audio" controls preload="none" src="%1$s"></audio><a class="stark-player__download" href="%1$s" download>Download MP3</a>',
        esc_url( $episode['audio'] )
    ) : '';

    if ( ! shortcode_exists( 'podlove-episode-web-player' ) ) {
        return $native ? '<div class="stark-player">' . $native . '</div>' : '';
    }

    // v27.3.17 — click-to-load Podlove Web Player. Its scripts cost ~770 ms of
    // main-thread time on mobile and it grows to ~1,000px after load (CLS 0.19).
    // The native player plays immediately; the full player (chapters, share,
    // transcript) loads on request. Its markup waits in an inert <template>.
    $podlove = do_shortcode( '[podlove-episode-web-player post_id="' . (int) $episode['id'] . '"]' );
    if ( ! $native ) return $podlove;
    // Its init waits for DOMContentLoaded, which has fired by the time it's injected — run it directly.
    $podlove = preg_replace( '/document\.addEventListener\(\s*"DOMContentLoaded"\s*,\s*function\s*\(\)\s*\{(.*)\}\s*\)\s*;?\s*<\/script>/s', '(function(){$1})();</script>', $podlove );
    $scripts = array();
    foreach ( array( 'podlove-web-player-player-cache', 'podlove-web-player-player' ) as $handle ) {
        $reg = wp_scripts()->registered[ $handle ] ?? null;
        if ( $reg ) $scripts[] = add_query_arg( 'ver', $reg->ver, $reg->src );
    }
    return '<div class="stark-player stark-player--deferred" data-scripts="' . esc_attr( wp_json_encode( $scripts ) ) . '">'
        . $native
        . '<button type="button" class="stark-player__more">Chapters &amp; full player</button>'
        . '<template class="stark-player__podlove">' . $podlove . '</template>'
        . '</div>';
}

/**
 * Related episodes: the nearest episodes by date (v27.4.12).
 */
function stark_podcast_related( $post_id, $count = 3 ) {
    // v27.4.12: the episodes either side of this one (older first), not the
    // newest three, so every episode is linked from its neighbours.
    $date  = get_post_field( 'post_date', $post_id );
    $base  = array( 'post_type' => 'podcast', 'post__not_in' => array( $post_id ), 'suppress_filters' => false );
    $older = get_posts( $base + array( 'numberposts' => $count, 'date_query' => array( array( 'before' => $date ) ), 'orderby' => 'date', 'order' => 'DESC' ) );
    $newer = get_posts( $base + array( 'numberposts' => $count, 'date_query' => array( array( 'after' => $date ) ), 'orderby' => 'date', 'order' => 'ASC' ) );
    $posts = array();
    for ( $i = 0; count( $posts ) < $count && ( isset( $older[ $i ] ) || isset( $newer[ $i ] ) ); $i++ ) {
        if ( isset( $older[ $i ] ) ) $posts[] = $older[ $i ];
        if ( isset( $newer[ $i ] ) && count( $posts ) < $count ) $posts[] = $newer[ $i ];
    }
    return $posts;
}

/**
 * Episode show notes sit under the template's "Show notes" h2, but old
 * episodes start their own headings at h4 (axe heading-order). Shift the
 * content's headings so the highest one is an h3. Render-time only.
 */
function stark_podcast_normalize_headings( $content ) {
    if ( ! is_singular( 'podcast' ) || ! in_the_loop() || ! preg_match_all( '/<h([1-6])\b/i', $content, $m ) ) return $content;
    $shift = 3 - min( array_map( 'intval', $m[1] ) );
    if ( 0 === $shift ) return $content;
    return preg_replace_callback( '#<(/?)h([1-6])\b#i', function ( $h ) use ( $shift ) {
        return '<' . $h[1] . 'h' . max( 3, min( 6, (int) $h[2] + $shift ) );
    }, $content );
}
add_filter( 'the_content', 'stark_podcast_normalize_headings', 20 );

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

/**
 * Podlove's player scripts load only on request (see stark_podcast_player()).
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! is_singular( 'podcast' ) ) return;
    wp_dequeue_script( 'podlove-web-player-player' );
    wp_dequeue_script( 'podlove-web-player-player-cache' );
}, 101 );

add_action( 'wp_footer', function () {
    if ( ! is_singular( 'podcast' ) ) return;
    ?>
<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.stark-player__more');
    if (!btn) return;
    var box = btn.closest('.stark-player'), tpl = box.querySelector('template.stark-player__podlove');
    var srcs = JSON.parse(box.getAttribute('data-scripts') || '[]');
    btn.disabled = true; btn.textContent = 'Loading player…';
    function load(i) {
        if (i >= srcs.length) return mount();
        var s = document.createElement('script'); s.src = srcs[i]; s.onload = function () { load(i + 1); }; s.onerror = mount;
        document.head.appendChild(s);
    }
    function mount() {
        var holder = document.createElement('div'); holder.className = 'stark-player__full';
        holder.appendChild(tpl.content.cloneNode(true));
        box.after(holder); // scripts cloned from a <template> run on insertion — once
        box.querySelector('audio') && box.querySelector('audio').pause();
        box.hidden = true;
    }
    load(0);
});
</script>
    <?php
}, 99 );

/**
 * Podlove's template auto-insert (live's settings, copied over): "top" injects a
 * second web player into the episode content and "bottom" injects a Cornerstone
 * component shortcode that Phase 2 can't render. The Phase 2 templates place the
 * player themselves, so both are turned off for this theme (the stored option is
 * untouched). v27.3.17
 */
add_filter( 'option_podlove_template_assignment', function ( $value ) {
    $value = (array) $value;
    $value['top']    = '0';
    $value['bottom'] = '0';
    return $value;
} );
