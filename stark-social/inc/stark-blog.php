<?php
/**
 * Stark 27 blog (v27.0.9)
 * Routes the posts index + post archives to templates/stark-blog-archive.php
 * and single posts to templates/stark-blog-single.php. Routed via
 * template_include (not root home.php/single.php) so attachment pages and
 * other post types keep their own templates.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_is_blog_archive() {
    return is_home() || is_category() || is_tag() || is_author() || is_date();
}

function stark_blog_template( $template ) {
    if ( is_front_page() && ! is_home() ) return $template;

    $file = null;
    if ( is_singular( 'post' ) ) {
        $file = 'stark-blog-single.php';
    } elseif ( stark_is_blog_archive() && ! is_front_page() && ! is_author() ) {
        // Author archives use templates/stark-author.php (inc/stark-authors.php).
        $file = 'stark-blog-archive.php';
    }

    if ( $file && file_exists( get_stylesheet_directory() . '/templates/' . $file ) ) {
        return get_stylesheet_directory() . '/templates/' . $file;
    }
    return $template;
}
add_filter( 'template_include', 'stark_blog_template', 99 );

function stark_body_class_blog( $classes ) {
    if ( is_singular( 'post' ) || ( stark_is_blog_archive() && ! is_front_page() ) ) {
        $classes[] = 'stark-has-hero';
        $classes[] = is_singular( 'post' ) ? 'stark-blog-single' : 'stark-blog-archive';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_blog' );

/**
 * Minutes to read, ~225 words per minute, minimum 1.
 */
function stark_reading_time( $post = null ) {
    $words = str_word_count( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post ) ) ) );
    return max( 1, (int) ceil( $words / 225 ) );
}

/**
 * First non-Uncategorized category, or null.
 */
function stark_primary_category( $post = null ) {
    foreach ( (array) get_the_category( get_post( $post )->ID ) as $cat ) {
        if ( 'uncategorized' !== $cat->slug ) return $cat;
    }
    return null;
}

/**
 * House account posts get no author bio box (matches Phase 1 author-box.js).
 */
function stark_show_author_box( $author_id ) {
    $login = get_the_author_meta( 'user_login', $author_id );
    return apply_filters( 'stark_show_author_box', 'starksocialmedia' !== $login, $author_id );
}

/**
 * Up to $count related posts: nearest in date within the primary category, then most recent.
 */
function stark_related_posts( $post_id, $count = 3 ) {
    // v27.4.3: the posts nearest in date within the category (older first, then
    // newer), not the newest three. Every post then gets links from its
    // neighbours instead of only the latest posts getting all of them.
    $cat     = stark_primary_category( $post_id );
    $date    = get_post_field( 'post_date', $post_id );
    $base    = array( 'post__not_in' => array( $post_id ), 'suppress_filters' => false );
    if ( $cat ) $base['category__in'] = array( $cat->term_id );

    $older = get_posts( $base + array( 'numberposts' => $count, 'date_query' => array( array( 'before' => $date ) ), 'orderby' => 'date', 'order' => 'DESC' ) );
    $newer = get_posts( $base + array( 'numberposts' => $count, 'date_query' => array( array( 'after' => $date ) ), 'orderby' => 'date', 'order' => 'ASC' ) );
    $posts = array();
    // Alternate older / newer, older first.
    for ( $i = 0; count( $posts ) < $count && ( isset( $older[ $i ] ) || isset( $newer[ $i ] ) ); $i++ ) {
        if ( isset( $older[ $i ] ) ) $posts[] = $older[ $i ];
        if ( isset( $newer[ $i ] ) && count( $posts ) < $count ) $posts[] = $newer[ $i ];
    }

    if ( count( $posts ) < $count ) {
        $posts = array_merge( $posts, get_posts( array(
            'numberposts'      => $count - count( $posts ),
            'post__not_in'     => array_merge( array( $post_id ), wp_list_pluck( $posts, 'ID' ) ),
            'suppress_filters' => false,
        ) ) );
    }
    return $posts;
}

/**
 * Archive hero copy for the current blog context.
 */
function stark_blog_archive_heading() {
    if ( is_category() || is_tag() ) {
        $term = get_queried_object();
        $desc = term_description();
        if ( ! trim( wp_strip_all_tags( $desc ) ) ) {
            // No description written yet: say what's here instead of leaving a gap (v27.3.8).
            $desc = sprintf( _n( '%1$d article on %2$s.', '%1$d articles on %2$s.', $term->count, 'stark-social' ), $term->count, $term->name );
        }
        return array( is_category() ? 'Category' : 'Tag', single_term_title( '', false ), $desc );
    }
    if ( is_author() ) {
        $author = get_queried_object();
        return array( 'Author', 'Posts by ' . $author->display_name, get_the_author_meta( 'description', $author->ID ) );
    }
    if ( is_date() ) {
        return array( 'Archive', wp_strip_all_tags( get_the_archive_title() ), '' );
    }
    $page_id = (int) get_option( 'page_for_posts' );
    return array( 'The Stark blog', $page_id ? get_the_title( $page_id ) : 'Blog', 'Practical marketing advice for small and mid-sized businesses: social, web, SEO and security.' );
}

/**
 * 12 posts per archive page so the 3-column grid fills evenly.
 */
function stark_blog_posts_per_page( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date() ) ) {
        $query->set( 'posts_per_page', 12 );
    }
}
add_action( 'pre_get_posts', 'stark_blog_posts_per_page' );

/**
 * /blog/category/{slug}/ and /blog/tag/{slug}/ → the real archives under
 * /blogs/… (live's category and tag bases, kept for SEO). v27.3.8
 */
function stark_redirect_blog_taxonomy_aliases() {
    if ( ! is_404() ) return;
    $path = trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' );
    if ( preg_match( '#^blog/(category|tag)/([^/]+)(/page/(\d+))?$#', $path, $m ) ) {
        $term = get_term_by( 'slug', $m[2], 'category' === $m[1] ? 'category' : 'post_tag' );
        if ( $term ) {
            $url = get_term_link( $term );
            if ( ! empty( $m[4] ) ) $url = trailingslashit( $url ) . 'page/' . (int) $m[4] . '/';
            wp_safe_redirect( $url, 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'stark_redirect_blog_taxonomy_aliases', 1 );
