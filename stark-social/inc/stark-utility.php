<?php
/**
 * Stark 27 utility templates (v27.0.10)
 * 404 → templates/stark-404.php, search → templates/stark-search.php.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_utility_template( $template ) {
    $file = is_404() ? 'stark-404.php' : ( is_search() ? 'stark-search.php' : null );
    if ( $file && file_exists( get_stylesheet_directory() . '/templates/' . $file ) ) {
        return get_stylesheet_directory() . '/templates/' . $file;
    }
    return $template;
}
add_filter( 'template_include', 'stark_utility_template', 99 );

function stark_body_class_utility( $classes ) {
    if ( is_404() || is_search() ) {
        $classes[] = 'stark-has-hero';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_utility' );

/**
 * Popular destinations for 404 and empty search states.
 */
function stark_popular_links() {
    return apply_filters( 'stark_popular_links', array(
        array( 'url' => '/web-design/', 'title' => 'Web Design' ),
        array( 'url' => '/seo/', 'title' => 'SEO' ),
        array( 'url' => '/social-media-management/', 'title' => 'Social Media Management' ),
        array( 'url' => '/blog/', 'title' => 'Blog' ),
        array( 'url' => '/about/', 'title' => 'About' ),
        array( 'url' => '/contact/', 'title' => 'Contact' ),
    ) );
}

/**
 * Search form in Stark style. $variant: 'light' (on dark hero) or 'default'.
 * $autofocus: focus the field on load (the 404 page, v27.4.8).
 */
function stark_search_form( $variant = 'default', $id = 'stark-search', $autofocus = false ) {
    ob_start(); ?>
    <form role="search" method="get" class="stark-search-form stark-search-form--<?php echo esc_attr( $variant ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>">Search the site</label>
        <input id="<?php echo esc_attr( $id ); ?>" class="stark-search-form__input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search posts and pages" autocomplete="off"<?php echo $autofocus ? ' autofocus' : ''; ?>>
        <button class="stark-btn stark-btn--primary stark-search-form__btn" type="submit">Search</button>
    </form>
    <?php
    return ob_get_clean();
}
