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
 * Up to $count related posts: same primary category first, then most recent.
 */
function stark_related_posts( $post_id, $count = 3 ) {
    $cat     = stark_primary_category( $post_id );
    $exclude = array( $post_id );
    $posts   = array();

    if ( $cat ) {
        $posts = get_posts( array(
            'numberposts'      => $count,
            'category__in'     => array( $cat->term_id ),
            'post__not_in'     => $exclude,
            'suppress_filters' => false,
        ) );
    }

    if ( count( $posts ) < $count ) {
        $posts = array_merge( $posts, get_posts( array(
            'numberposts'      => $count - count( $posts ),
            'post__not_in'     => array_merge( $exclude, wp_list_pluck( $posts, 'ID' ) ),
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
        return array( is_category() ? 'Category' : 'Tag', single_term_title( '', false ), term_description() );
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
