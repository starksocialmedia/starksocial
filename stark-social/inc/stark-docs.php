<?php
/**
 * Stark 27 BetterDocs landmarks (v27.4.0)
 *
 * BetterDocs' single and archive templates have no <main>, so axe flagged
 * "region" (content outside landmarks), a second banner (the article
 * <header> counted as one) and two unlabelled <aside>s. Plugin files stay
 * untouched: BetterDocs' chosen template is rendered through
 * templates/stark-docs-wrap.php, which buffers it and fixes the markup.
 * If a plugin update changes the markers, the page renders unchanged.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function stark_is_docs_view() {
    return is_singular( 'docs' ) || is_post_type_archive( 'docs' ) || is_tax( array( 'doc_category', 'doc_tag', 'knowledge_base' ) );
}

function stark_docs_wrap_template( $template ) {
    if ( ! is_string( $template ) || ! is_file( $template ) ) return $template;
    $GLOBALS['stark_docs_inner_template'] = $template;
    return get_stylesheet_directory() . '/templates/stark-docs-wrap.php';
}
add_filter( 'betterdocs_single_template', 'stark_docs_wrap_template', 99 );
add_filter( 'betterdocs_archives_template', 'stark_docs_wrap_template', 99 );

// BetterDocs templates call get_footer() right after their wrapper closes; mark that spot.
add_action( 'get_footer', function () {
    if ( ! empty( $GLOBALS['stark_docs_inner_template'] ) ) echo '<!--stark-docs-end-->';
}, 0 );

/**
 * Fix the buffered page. Singles: the article column becomes <main> (the
 * sidebars stay top-level <aside>s, now labelled). Archives have no article
 * column, so <main> wraps the whole BetterDocs wrapper.
 */
function stark_docs_landmarks( $html ) {
    $raw = $html;
    if ( false !== strpos( $html, 'id="betterdocs-single-main"' ) ) {
        // BetterDocs' id stays (its CSS and JS use it); the skip link targets it.
        $html = preg_replace( '/<div(\s+id="betterdocs-single-main"[^>]*)>/', '<main$1>', $html, 1, $opened );
        $html = preg_replace( '#</div>(\s*<!-- \#main -->)#', '</main>$1', $html, 1, $closed );
        if ( ! $opened || ! $closed ) return $raw;
        // h4 after the article's h2s skipped a level.
        $html = preg_replace( '#<h4( class="betterdocs-social-share-title-tag">.*?)</h4>#s', '<h2$1</h2>', $html, 1 );
    } else {
        $html = preg_replace( '/(<div class="betterdocs-wrapper)/', '<main id="main" class="stark-docs-main">$1', $html, 1, $opened );
        $html = str_replace( '<!--stark-docs-end-->', $opened ? '</main>' : '', $html, $closed );
        if ( ! $opened || ! $closed ) return str_replace( '<!--stark-docs-end-->', '', $raw );
    }
    $html = str_replace( '<!--stark-docs-end-->', '', $html );
    $left = '/<aside((?:\s+[a-z-]+="[^"]*")*?\s+id="betterdocs-full-sidebar-left"[^>]*)>(.*?)<\/aside>/s';
    if ( false === strpos( $html, 'id="betterdocs-single-main"' ) ) {
        // Archives: the category list sits inside <main>, where it is navigation, not a complementary landmark.
        $html = preg_replace( $left, '<nav aria-label="Knowledgebase categories"$1>$2</nav>', $html, 1 );
    } else {
        $html = preg_replace( $left, '<aside aria-label="Knowledgebase categories"$1>$2</aside>', $html, 1 );
    }
    $html = preg_replace( '/<aside(\s+id="betterdocs-sidebar-right")/', '<aside aria-label="On this page"$1', $html, 1 );
    return $html;
}

add_filter( 'stark_skip_link_target', function ( $target ) {
    return is_singular( 'docs' ) ? '#betterdocs-single-main' : $target;
} );

// The Stark header prints the skip link on these pages; GeneratePress's points at #content, outside any landmark.
add_action( 'wp', function () {
    if ( stark_is_docs_view() ) remove_action( 'generate_before_header', 'generate_do_skip_to_content_link', 2 );
} );
