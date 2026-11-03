<?php
/**
 * Stark 27 admin + editorial experience (v27.3.11)
 * - Branded login page (existing theme logo file, navy background)
 * - "Stark Social 27" admin footer
 * - "Stark" admin colour scheme, used when a user hasn't picked one
 * - Dashboard widget: recent form submissions, posts waiting for review,
 *   pages whose Copy status isn't Approved
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Login page ------------------------------------------------------------- */
add_action( 'login_head', function () { // after core login CSS, so these win
    $logo = get_stylesheet_directory_uri() . '/img/stark-logo-light.svg';
    ?>
    <style>
        body.login { background: #031B34; font-family: "Barlow", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .login h1 a { background: url(<?php echo esc_url( $logo ); ?>) no-repeat center / contain; width: 280px; height: 48px; }
        .login form { border: 0; border-radius: 16px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25); }
        .login .button-primary { background: #2A70CC; border-color: #2A70CC; border-radius: 10px; font-weight: 600; }
        .login .button-primary:hover, .login .button-primary:focus { background: #1F5DAE; border-color: #1F5DAE; }
        .login input:focus { border-color: #2A70CC; box-shadow: 0 0 0 1px #2A70CC; }
        .login #nav a, .login #backtoblog a, .login .privacy-policy-link { color: rgba(255, 255, 255, 0.78); }
        .login #nav a:hover, .login #backtoblog a:hover { color: #fff; }
    </style>
    <?php
} );
add_filter( 'login_headerurl', function () { return home_url( '/' ); } );
add_filter( 'login_headertext', function () { return get_bloginfo( 'name' ); } );

/* Admin footer ----------------------------------------------------------- */
add_filter( 'admin_footer_text', function () {
    return 'Stark Social 27 · <a href="' . esc_url( home_url( '/' ) ) . '">starksocial.com</a>';
} );

/* Colour scheme ---------------------------------------------------------- */
add_action( 'admin_init', function () {
    wp_admin_css_color( 'stark', 'Stark',
        admin_url( 'css/colors/midnight/colors.min.css' ), // base sheet; brand colours below
        array( '#031B34', '#0B3463', '#2A70CC', '#F93822' ),
        array( 'base' => '#c3cad3', 'focus' => '#fff', 'current' => '#fff' )
    );
} );
// Users who never chose a scheme ("fresh" is WordPress's default) get Stark.
add_filter( 'get_user_option_admin_color', function ( $color ) {
    return ( ! $color || 'fresh' === $color ) ? 'stark' : $color;
} );
add_action( 'admin_head', function () {
    if ( 'stark' !== get_user_option( 'admin_color' ) ) return;
    echo '<style>#adminmenu, #adminmenuback, #adminmenuwrap { background: #031B34; } #adminmenu .wp-submenu { background: #0B3463 !important; } #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu { background: #2A70CC !important; } #wpadminbar { background: #031B34; } .wp-core-ui .button-primary { background: #2A70CC; border-color: #2A70CC; } .wp-core-ui .button-primary:hover { background: #1F5DAE; border-color: #1F5DAE; }</style>';
} );

/* Dashboard widget ------------------------------------------------------- */
add_action( 'wp_dashboard_setup', function () {
    if ( ! current_user_can( 'edit_pages' ) ) return;
    wp_add_dashboard_widget( 'stark_review_widget', 'Stark — needs attention', 'stark_dashboard_widget' );
    // Put it first.
    global $wp_meta_boxes;
    $normal = $wp_meta_boxes['dashboard']['normal']['core'] ?? array();
    if ( isset( $normal['stark_review_widget'] ) ) {
        $wp_meta_boxes['dashboard']['normal']['core'] = array( 'stark_review_widget' => $normal['stark_review_widget'] ) + $normal;
    }
} );

function stark_dashboard_widget() {
    $list = function ( $title, $rows, $empty ) {
        echo '<h3 style="margin:16px 0 6px;font-weight:600;">' . esc_html( $title ) . '</h3>';
        if ( ! $rows ) { echo '<p style="margin:0;color:#646970;">' . esc_html( $empty ) . '</p>'; return; }
        echo '<ul style="margin:0;">';
        foreach ( $rows as $row ) {
            echo '<li style="margin:0 0 6px;"><a href="' . esc_url( $row[1] ) . '">' . esc_html( $row[0] ) . '</a>' . ( ! empty( $row[2] ) ? ' <span style="color:#646970;">— ' . esc_html( $row[2] ) . '</span>' : '' ) . '</li>';
        }
        echo '</ul>';
    };

    // Recent form submissions (Gravity Forms).
    $entries = array();
    if ( class_exists( 'GFAPI' ) ) {
        foreach ( GFAPI::get_entries( 0, array( 'status' => 'active' ), array( 'key' => 'date_created', 'direction' => 'DESC' ), array( 'page_size' => 5 ) ) as $e ) {
            $form = GFAPI::get_form( $e['form_id'] );
            $who  = trim( ( $e['1'] ?? '' ) . ( ! empty( $e['2'] ) ? ', ' . $e['2'] : '' ) ) ?: 'Entry #' . $e['id'];
            $entries[] = array( $who, admin_url( 'admin.php?page=gf_entries&view=entry&id=' . $e['form_id'] . '&lid=' . $e['id'] ), mysql2date( 'M j, g:i a', $e['date_created'] ) . ( $form ? ' · ' . $form['title'] : '' ) );
        }
    }
    $list( 'Recent form submissions', $entries, 'No submissions yet.' );

    // Posts waiting for review.
    $posts = array_map( function ( $p ) {
        return array( get_the_title( $p ) ?: '(no title)', get_edit_post_link( $p ), ucfirst( $p->post_status ) . ' · ' . get_the_modified_date( 'M j', $p ) );
    }, get_posts( array( 'post_type' => array( 'post', 'podcast', 'portfolio' ), 'post_status' => array( 'pending', 'draft' ), 'numberposts' => 6, 'orderby' => 'modified' ) ) );
    $list( 'Posts waiting for review', $posts, 'Nothing pending or in draft.' );

    // Pages whose copy isn't approved.
    $pages = array();
    global $wpdb;
    $ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'copy_status' AND meta_value <> 'approved'" );
    // Service pages with no Copy status saved yet count as Draft.
    foreach ( array_keys( function_exists( 'stark_get_services' ) ? stark_get_services() : array() ) as $slug ) {
        $page = get_page_by_path( $slug );
        if ( $page && '' === (string) get_post_meta( $page->ID, 'copy_status', true ) ) $ids[] = $page->ID;
    }
    foreach ( $ids as $id ) {
        if ( 'publish' !== get_post_status( $id ) ) continue;
        $status = get_post_meta( $id, 'copy_status', true );
        $pages[] = array( get_the_title( $id ), get_edit_post_link( $id ), 'live' === $status ? 'Live-sourced — voice review' : 'Draft' );
    }
    $list( 'Pages to approve (Copy status)', $pages, 'All page copy is approved.' );
}

/**
 * Favicon suite (v27.4.6): img/favicon/, all resized from live's site icon
 * (tools/images/make-favicons.py). Replaces WordPress's site-icon tags (WebP
 * sizes, which iOS and older browsers don't use for icons). The site icon
 * setting stays as the source for the admin and login screens.
 */
function stark_favicon_tags() {
    $base = get_stylesheet_directory_uri() . '/img/favicon/';
    return array(
        '<link rel="icon" href="' . esc_url( $base . 'favicon.ico' ) . '" sizes="48x48">',
        '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $base . 'favicon-32x32.png' ) . '">',
        '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url( $base . 'favicon-16x16.png' ) . '">',
        '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url( $base . 'apple-touch-icon.png' ) . '">',
        '<link rel="manifest" href="' . esc_url( $base . 'site.webmanifest' ) . '">',
        '<meta name="msapplication-TileColor" content="#031b34">',
        '<meta name="msapplication-config" content="' . esc_url( $base . 'browserconfig.xml' ) . '">',
    );
}
add_filter( 'site_icon_meta_tags', function () {
    return stark_favicon_tags();
} );

// Without a site icon WordPress prints nothing; print the suite anyway.
add_action( 'wp_head', function () {
    if ( ! has_site_icon() ) echo implode( "\n", stark_favicon_tags() ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
}, 99 );

// /favicon.ico: serve the multi-size ICO instead of WordPress's redirect to the 32px WebP.
add_action( 'do_faviconico', function () {
    $file = get_stylesheet_directory() . '/img/favicon/favicon.ico';
    if ( ! is_readable( $file ) ) return;
    header( 'Content-Type: image/vnd.microsoft.icon' );
    header( 'Cache-Control: public, max-age=604800' );
    header( 'Content-Length: ' . filesize( $file ) );
    readfile( $file );
    exit;
} );
