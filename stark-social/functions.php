<?php
/**
 * FUNCTIONS.PHP — Stark Social (GeneratePress child theme)
 * Phase 2 rebuild (April 2026) — migrated from Themeco Pro/X + Cornerstone.
 *
 * Blog URLs + pagination: handled by WP Permalinks + the rewrite rules
 * registered at the bottom of this file. Verify:
 * - Settings → Permalinks: /blog/%postname%/
 * - Settings → Reading: Posts page = Blog
 *
 * Notes:
 * - Cloudflare Enterprise handles HTTPS redirects; do NOT force_https() in PHP.
 * - Do NOT store API keys in functions.php. Use wp-config.php constants.
 * - Staging flag: define( 'STARK_ENV', 'staging' ); in staging wp-config.php.
 *   Production integrations (GF Webhooks → Perfex) only fire when !STARK_IS_STAGING.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Environment + version constants.
 */
if ( ! defined( 'STARK_CHILD_VERSION' ) ) {
    define( 'STARK_CHILD_VERSION', '2.0.0' );
}
if ( ! defined( 'STARK_ENV' ) ) {
    define( 'STARK_ENV', 'production' );
}
define( 'STARK_IS_STAGING', STARK_ENV === 'staging' );

/**
 * Seasonal accent — set <html data-stark-season="..."> before paint so
 * custom.css has the right accent on first render (no flash).
 */
add_action( 'wp_head', function () {
    ?>
<script>(function(){var m=new Date().getMonth(),s='winter';if(m>=2&&m<=4)s='spring';else if(m>=5&&m<=7)s='summer';else if(m>=8&&m<=10)s='fall';document.documentElement.setAttribute('data-stark-season',s);})();</script>
    <?php
}, 0 );

/**
 * Enqueue child stylesheet + global design system CSS.
 * GeneratePress parent handle is 'generate-style'.
 */
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'stark-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'generate-style' ),
        STARK_CHILD_VERSION
    );
    wp_enqueue_style(
        'stark-custom',
        get_stylesheet_directory_uri() . '/custom.css',
        array( 'stark-child' ),
        STARK_CHILD_VERSION
    );
}, 20 );

/**
 * Preload primary Barlow weights (self-hosted WOFF2 in /fonts/).
 */
add_action( 'wp_head', function () {
    $fonts_uri = get_stylesheet_directory_uri() . '/fonts';
    $preload   = array(
        'barlow-v13-latin-regular.woff2',
        'barlow-v13-latin-700.woff2',
        'barlow-v13-latin-800.woff2',
    );
    foreach ( $preload as $file ) {
        printf(
            '<link rel="preload" as="font" type="font/woff2" href="%s/%s" crossorigin="anonymous">' . "\n",
            esc_url( $fonts_uri ),
            esc_attr( $file )
        );
    }
}, 1 );

/**
 * Enqueue global JS — scroll progress, velocity, seasonal accents,
 * A11y + back-to-top choreography, reveal/tilt.
 * Loaded in footer, deferred.
 */
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'stark-global',
        get_stylesheet_directory_uri() . '/js/stark-global.js',
        array(),
        STARK_CHILD_VERSION,
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
    wp_enqueue_script(
        'stark-reveal',
        get_stylesheet_directory_uri() . '/js/stark-reveal.js',
        array(),
        STARK_CHILD_VERSION,
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}, 20 );

/**
 * Register Podcast custom post type.
 */
function stark_register_podcast_post_type() {
    $labels = array(
        'name'               => _x( 'Podcasts', 'post type general name' ),
        'singular_name'      => _x( 'Podcast', 'post type singular name' ),
        'menu_name'          => _x( 'Podcasts', 'admin menu' ),
        'name_admin_bar'     => _x( 'Podcast', 'add new on admin bar' ),
        'add_new'            => _x( 'Add New', 'podcast' ),
        'add_new_item'       => __( 'Add New Podcast' ),
        'new_item'           => __( 'New Podcast' ),
        'edit_item'          => __( 'Edit Podcast' ),
        'view_item'          => __( 'View Podcast' ),
        'all_items'          => __( 'All Podcasts' ),
        'search_items'       => __( 'Search Podcasts' ),
        'parent_item_colon'  => __( 'Parent Podcasts:' ),
        'not_found'          => __( 'No podcasts found.' ),
        'not_found_in_trash' => __( 'No podcasts found in Trash.' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'podcast', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'taxonomies'         => array( 'category', 'post_tag' ),
    );

    register_post_type( 'podcast', $args );
}
add_action( 'init', 'stark_register_podcast_post_type' );

/**
 * Flush rewrite rules on theme activation.
 */
function stark_flush_rewrite_rules_on_theme_activation() {
    stark_register_podcast_post_type();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'stark_flush_rewrite_rules_on_theme_activation' );


/**
 * Add data-author-login attribute on <html> for single posts (cached via transient).
 */
function stark_add_author_login_attribute() {
    if ( is_admin() || ! is_singular( 'post' ) ) {
        return;
    }

    $post_id       = (int) get_queried_object_id();
    $transient_key = 'stark_author_login_' . $post_id;
    $login         = get_transient( $transient_key );

    if ( false === $login ) {
        $author_id = (int) get_post_field( 'post_author', $post_id );
        if ( ! $author_id ) {
            return;
        }

        $user = get_userdata( $author_id );
        if ( ! $user || empty( $user->user_login ) ) {
            return;
        }

        $login = sanitize_key( $user->user_login );
        set_transient( $transient_key, $login, DAY_IN_SECONDS );
    }

    wp_enqueue_script( 'wp-util' );
    wp_add_inline_script(
        'wp-util',
        'document.documentElement.setAttribute("data-author-login","' . esc_js( $login ) . '");',
        'after'
    );
}
add_action( 'wp_enqueue_scripts', 'stark_add_author_login_attribute', 20 );

/**
 * Export filters: cast nulls to safe strings.
 */
function stark_export_null_cast_filters( $value ) {
    return ( null === $value ) ? '' : (string) $value;
}
add_filter( 'the_title_export', 'stark_export_null_cast_filters', 999 );
add_filter( 'the_content_export', 'stark_export_null_cast_filters', 999 );
add_filter( 'the_excerpt_export', 'stark_export_null_cast_filters', 999 );
add_filter( 'term_description', 'stark_export_null_cast_filters', 999 );

add_filter( 'get_term_metadata', function( $meta ) {
    return ( null === $meta ) ? '' : $meta;
}, 999 );

/**
 * Add body class on single posts for safe CSS scoping.
 */
function stark_body_class_single_post( $classes ) {
    if ( is_singular( 'post' ) ) {
        $classes[] = 'stark-single-post';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_single_post' );

/**
 * Conditionally enqueue Password Generator assets (page slug: password-generator).
 */
function stark_enqueue_passgen_assets() {
    if ( is_admin() || ! is_page( 'password-generator' ) ) {
        return;
    }
    wp_enqueue_style( 'stark-passgen-style', get_stylesheet_directory_uri() . '/passgen/passgen.css', array(), STARK_CHILD_VERSION );
    wp_enqueue_script( 'stark-passgen-script', get_stylesheet_directory_uri() . '/passgen/passgen.js', array(), STARK_CHILD_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'stark_enqueue_passgen_assets' );

/**
 * Shortcode: [password_generator] includes passgen.php.
 */
function stark_password_generator_shortcode() {
    $file = get_stylesheet_directory() . '/passgen/passgen.php';
    if ( ! file_exists( $file ) ) {
        return '';
    }
    ob_start();
    include $file;
    return ob_get_clean();
}
add_shortcode( 'password_generator', 'stark_password_generator_shortcode' );

/**
 * Enqueue author-box.js on posts that actually have author bio blocks.
 * (Originally in Phase 1 — script depends on bio markup from AIOSEO or author widget.)
 */
function stark_enqueue_author_box_script() {
    if ( is_admin() || ! is_singular( array( 'post', 'podcast' ) ) ) {
        return;
    }
    wp_enqueue_script(
        'stark-author-box',
        get_stylesheet_directory_uri() . '/js/author-box.js',
        array(),
        STARK_CHILD_VERSION,
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}
add_action( 'wp_enqueue_scripts', 'stark_enqueue_author_box_script' );

/**
 * Enqueue excerpt-length.js on podcast archive (truncates to 50 words).
 */
function stark_enqueue_excerpt_length_script() {
    if ( is_admin() ) {
        return;
    }
    if ( is_post_type_archive( 'podcast' ) || is_tax( array( 'podcast_category', 'podcast_tag' ) ) ) {
        wp_enqueue_script(
            'stark-excerpt-length',
            get_stylesheet_directory_uri() . '/js/excerpt-length.js',
            array(),
            STARK_CHILD_VERSION,
            array( 'in_footer' => true, 'strategy' => 'defer' )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'stark_enqueue_excerpt_length_script' );

/**
 * Disable BetterDocs CSS styles site-wide.
 */
function stark_disable_betterdocs_styles() {
    wp_dequeue_style( 'betterdocs-frontend' );
    wp_dequeue_style( 'betterdocs-pro-frontend' );
    wp_dequeue_style( 'betterdocs-icon-font' );
    wp_dequeue_style( 'betterdocs-single' );
    wp_dequeue_style( 'betterdocs-archive' );
    wp_dequeue_style( 'betterdocs-search' );
}
add_action( 'wp_enqueue_scripts', 'stark_disable_betterdocs_styles', 100 );

/**
 * Disable Gravity Forms CSS styles site-wide.
 */
function stark_disable_gravityforms_styles() {
    wp_dequeue_style( 'gforms_css' );
    wp_dequeue_style( 'gform_basic' );
    wp_dequeue_style( 'gform_theme_css' );
    wp_dequeue_style( 'gforms_browsers_css' );
}
add_action( 'wp_enqueue_scripts', 'stark_disable_gravityforms_styles', 100 );
add_filter( 'gform_disable_css', '__return_true' );

/**
 * TODO [PHASE 2 / RankMath port] — Podcast breadcrumbs for RankMath.
 *
 * Phase 1 implementation targeted AIOSEO via the 'aioseo_breadcrumbs_trail' filter
 * (output: Home > Podcast > Title for single, Home > Podcast for archive).
 *
 * Port to RankMath using the 'rank_math/frontend/breadcrumb/items' filter once
 * RankMath Pro is installed on staging. Reference structure:
 *   is_post_type_archive('podcast')  -> [ Home, {Podcast} ]
 *   is_singular('podcast')           -> [ Home, {Podcast}, {Episode Title} ]
 *
 * Original Phase 1 callback preserved below in comments for reference.
 */
// add_filter( 'aioseo_breadcrumbs_trail', function( $crumbs ) {
//     $normalize = function( $crumb, $defaults = array() ) {
//         $base = array( 'label' => '', 'link' => '', 'type' => '', 'subType' => '', 'reference' => null );
//         return array_merge( $base, (array) $crumb, (array) $defaults );
//     };
//     $home = isset( $crumbs[0] )
//         ? $normalize( $crumbs[0], array( 'type' => 'home' ) )
//         : $normalize( array( 'label' => 'Home', 'link' => home_url( '/' ) ), array( 'type' => 'home' ) );
//     $podcastCrumb = $normalize(
//         array( 'label' => 'Podcast', 'link' => home_url( '/podcast/' ) ),
//         array( 'type' => 'postTypeArchive', 'subType' => 'podcast', 'reference' => 'podcast' )
//     );
//     if ( is_post_type_archive( 'podcast' ) ) {
//         return array( $home, $podcastCrumb );
//     }
//     if ( is_singular( 'podcast' ) ) {
//         $post_id = (int) get_queried_object_id();
//         $titleCrumb = $normalize(
//             array( 'label' => get_the_title( $post_id ), 'link' => get_permalink( $post_id ) ),
//             array( 'type' => 'single', 'subType' => 'podcast', 'reference' => $post_id )
//         );
//         return array( $home, $podcastCrumb, $titleCrumb );
//     }
//     return $crumbs;
// }, 200 );

/**
 * Helpers: seconds -> H:MM:SS or M:SS
 */
function stark_seconds_to_hms( $seconds ) {
    $seconds = (int) $seconds;
    if ( $seconds <= 0 ) {
        return '';
    }
    $h = (int) floor( $seconds / 3600 );
    $m = (int) floor( ( $seconds % 3600 ) / 60 );
    $s = (int) ( $seconds % 60 );

    return ( $h > 0 )
        ? sprintf( '%d:%02d:%02d', $h, $m, $s )
        : sprintf( '%d:%02d', $m, $s );
}

/**
 * Podlove Meta Shortcode: [starkpodmeta]
 * Output: Episode {number} • {duration} • {date}
 */
function starkpodmeta_shortcode() {
    $post_id = (int) get_the_ID();

    if ( ! $post_id || 'podcast' !== get_post_type( $post_id ) ) {
        return get_the_date( 'M j, Y', $post_id );
    }

    $transient_key = 'stark_podmeta_' . $post_id;
    $output        = get_transient( $transient_key );

    if ( false === $output ) {
        global $wpdb;

        $episode_table = $wpdb->prefix . 'podlove_episode';
        $table_exists  = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $episode_table ) );

        if ( $table_exists !== $episode_table ) {
            $output = get_the_date( 'M j, Y', $post_id );
            set_transient( $transient_key, $output, DAY_IN_SECONDS );
            return $output;
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT number, duration FROM `$episode_table` WHERE post_id = %d LIMIT 1",
                $post_id
            )
        );

        $number   = ( $row && isset( $row->number ) ) ? (string) $row->number : '';
        $duration = ( $row && isset( $row->duration ) ) ? $row->duration : '';

        $dur_out = '';
        if ( is_string( $duration ) ) {
            $duration = trim( $duration );
            if ( preg_match( '/^\d{1,2}:\d{2}(:\d{2})?$/', $duration ) ) {
                $dur_out = $duration;
            } elseif ( is_numeric( $duration ) ) {
                $dur_out = stark_seconds_to_hms( (int) $duration );
            }
        } elseif ( is_numeric( $duration ) ) {
            $dur_out = stark_seconds_to_hms( (int) $duration );
        }

        $date  = get_the_date( 'M j, Y', $post_id );
        $parts = array();

        if ( $number && '0' !== $number ) {
            $parts[] = 'Episode ' . esc_html( $number );
        }
        if ( $dur_out ) {
            $parts[] = esc_html( $dur_out );
        }
        if ( $date ) {
            $parts[] = esc_html( $date );
        }

        $output = implode( ' • ', $parts );
        set_transient( $transient_key, $output, DAY_IN_SECONDS );
    }

    return $output;
}
add_shortcode( 'starkpodmeta', 'starkpodmeta_shortcode' );

/**
 * Invalidate transients on podcast save/update.
 */
add_action( 'save_post_podcast', function( $post_id ) {
    $post_id = (int) $post_id;
    if ( wp_is_post_revision( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
        return;
    }
    delete_transient( 'stark_podmeta_' . $post_id );
    delete_transient( 'stark_author_login_' . $post_id );
}, 10, 1 );

/**
 * Weekly DB prune event.
 *
 * Phase 2 change: AIOSEO cache truncation removed — RankMath replaces AIOSEO.
 * If RankMath ships a cache table that needs periodic truncation, add it here.
 */
add_action( 'nate_prune_db_event', function() {
    global $wpdb;

    $wpdb->query( "DELETE FROM {$wpdb->prefix}podlove_downloadintent WHERE accessed_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)" );
    $wpdb->query( "DELETE FROM {$wpdb->prefix}podlove_downloadintentclean WHERE accessed_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)" );
    $wpdb->query( "OPTIMIZE TABLE {$wpdb->prefix}podlove_downloadintent, {$wpdb->prefix}podlove_downloadintentclean" );

    $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}betterdocs_search_log" );
    $wpdb->query( "OPTIMIZE TABLE {$wpdb->prefix}betterdocs_search_log" );
} );

if ( ! wp_next_scheduled( 'nate_prune_db_event' ) ) {
    wp_schedule_event( time(), 'weekly', 'nate_prune_db_event' );
}

/**
 * Preload featured image on podcast singles (LCP assist).
 */
add_action( 'wp_head', function() {
    if ( is_singular( 'podcast' ) && has_post_thumbnail() ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
        if ( is_array( $img ) && ! empty( $img[0] ) ) {
            echo '<link rel="preload" as="image" href="' . esc_url( $img[0] ) . '">' . "\n";
        }
    }
}, 1 );

/**
 * Security headers. HSTS only emitted on production HTTPS.
 */
function stark_add_security_headers() {
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'X-XSS-Protection: 1; mode=block' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
    if ( ! STARK_IS_STAGING && ! empty( $_SERVER['HTTPS'] ) ) {
        header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
    }
}
add_action( 'send_headers', 'stark_add_security_headers' );

remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'wp_headers', function( $headers ) { unset( $headers['X-Pingback'] ); return $headers; } );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Only load podcast/web-player scripts on podcast pages.
 */
add_action('wp_enqueue_scripts', function () {

    $is_podcast_page =
        is_singular('podcast') ||
        is_post_type_archive('podcast') ||
        ( is_tax() && (is_tax('podcast_category') || is_tax('podcast_tag')) ) ||
        is_page('podcast');

    if ( $is_podcast_page ) {
        return;
    }

    $handles = array(
        'web-player',
        'web-player-embed',
        'podlove-web-player',
        'podlove-web-player-embed',
        'podlove-player',
        'podlove-player-embed',
    );

    foreach ( $handles as $h ) {
        wp_dequeue_script($h);
        wp_deregister_script($h);
    }

}, 100);

/**
 * Remove MediaElement unless the page actually needs it.
 */
add_action('wp_enqueue_scripts', function () {

    if ( is_singular('podcast') || is_post_type_archive('podcast') || is_page('podcast') ) {
        return;
    }

    wp_dequeue_script('wp-mediaelement');
    wp_dequeue_style('wp-mediaelement');

    wp_dequeue_script('mediaelement-core');
    wp_dequeue_script('mediaelement-migrate');
    wp_dequeue_style('mediaelement');
}, 100);

add_action('wp_enqueue_scripts', function () {
    if ( ! is_admin() ) {
        wp_dequeue_style('admin-font');
    }
}, 100);

add_action('wp_enqueue_scripts', function () {
    if ( is_front_page() ) {
        wp_dequeue_script('masonry');
        wp_dequeue_script('imagesloaded');
    }
}, 100);

/**
 * Remove comment support from all post types.
 */
add_action('init', function () {
    foreach ( get_post_types() as $post_type ) {
        if ( post_type_supports($post_type, 'comments') ) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

/**
 * Blog rewrite rules — /blog/ and /blog/page/N/
 * (Phase 1 comment claimed "no custom rewrites" but rules were present; comment corrected in Phase 2.)
 */
add_action('init', function () {
    add_rewrite_rule('^blog/?$', 'index.php?pagename=blog', 'top');
    add_rewrite_rule('^blog/page/([0-9]+)/?$', 'index.php?pagename=blog&paged=$matches[1]', 'top');
}, 1);

/**
 * Fix BetterDocs breadcrumb /docs/ URL to /support/knowledgebase/
 */
add_action('template_redirect', function() {
    if (is_singular('docs') || is_tax('doc_category') || is_tax('doc_tag')) {
        ob_start(function($html) {
            return str_replace(
                'href="' . home_url('/docs/') . '"',
                'href="' . home_url('/support/knowledgebase/') . '"',
                $html
            );
        });
    }
});

/**
 * Disable wpautop on Gravity Forms notification emails.
 * (ERR-001 — Apple Mail dark mode + Spark rendering.)
 */
add_filter( 'gform_pre_send_email', function( $email ) {
    $email['body'] = str_replace(
        array( '<br />', '<br/>', '<br>' ),
        '',
        $email['body']
    );
    return $email;
}, 1, 1 );

/**
 * Staging-only: red "⚠ STAGING" node in the admin bar so you always know
 * which environment you are looking at.
 */
if ( STARK_IS_STAGING ) {
    add_action( 'admin_bar_menu', function ( $bar ) {
        $bar->add_node( array(
            'id'    => 'stark-env',
            'title' => '⚠ STAGING',
            'meta'  => array( 'class' => 'stark-env-staging' ),
        ) );
    }, 100 );

    $badge_css = '<style>#wp-admin-bar-stark-env > .ab-item{background:#F93822 !important;color:#fff !important;font-weight:700;}</style>';
    add_action( 'admin_head', function () use ( $badge_css ) { echo $badge_css; } );
    add_action( 'wp_head',    function () use ( $badge_css ) { echo $badge_css; } );
}
/**
 * Bottom UI cluster — A11y + back-to-top + Perfex chat embed.
 * Appended to functions.php by apply.sh.
 */

function stark_enqueue_bottom_ui_script() {
	if ( is_admin() ) return;
	wp_enqueue_script(
		'stark-bottom-ui',
		get_stylesheet_directory_uri() . '/js/stark-bottom-ui.js',
		array(),
		defined( 'STARK_CHILD_VERSION' ) ? STARK_CHILD_VERSION : null,
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);
}
add_action( 'wp_enqueue_scripts', 'stark_enqueue_bottom_ui_script', 20 );

function stark_render_bottom_ui_cluster() {
	if ( is_admin() ) return;
	?>
<a href="/support/accessibility-statement/" class="stark-a11y-btn" aria-label="Accessibility Statement and Support" title="Stark Social Media Agency Accessibility Statement"><span class="stark-a11y-icon" aria-hidden="true">A11y</span></a>
<button type="button" class="stark-back-to-top" aria-label="Back to top" title="Back to top"><span class="screen-reader-text">Back to top</span></button>
	<?php
}
add_action( 'wp_footer', 'stark_render_bottom_ui_cluster', 5 );

function stark_inject_perfex_chat_widget() {
	if ( is_admin() ) return;
	if ( defined( 'STARK_IS_STAGING' ) && STARK_IS_STAGING ) {
		if ( ! defined( 'STARK_PRCHAT_ON_STAGING' ) || ! STARK_PRCHAT_ON_STAGING ) {
			return;
		}
	}
	$widget_id = defined( 'STARK_PRCHAT_WIDGET_ID' )
		? STARK_PRCHAT_WIDGET_ID
		: '67d3563318b5547bfab53fa6366ace82';
	$widget_url = 'https://hub.starksocial.com/prchat/Chatbot_Controller/widget/' . rawurlencode( $widget_id );
	printf(
		'<script type="text/javascript">(function(){var d=document,s=d.createElement("script");s.src=%s;s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>',
		wp_json_encode( esc_url_raw( $widget_url ) )
	);
}
add_action( 'wp_footer', 'stark_inject_perfex_chat_widget', 20 );
/**
 * Stark 27 Nav — register menus, enqueue nav JS, render header
 * Added November 2 2026 (v27.0.0).
 */

/**
 * Register Phase 2 nav menu locations.
 */
function stark_register_nav_menus() {
    register_nav_menus( array(
        'stark-primary' => __( 'Stark Primary Navigation', 'stark-social' ),
        'stark-utility' => __( 'Stark Utility Navigation', 'stark-social' ),
    ) );
}
add_action( 'after_setup_theme', 'stark_register_nav_menus' );

/**
 * Load the mega walker class.
 */
require_once get_stylesheet_directory() . '/inc/class-stark-mega-walker.php';

/**
 * Enqueue nav JS, deferred in footer.
 */
function stark_enqueue_nav_script() {
    if ( is_admin() ) return;
    wp_enqueue_script(
        'stark-nav',
        get_stylesheet_directory_uri() . '/js/stark-nav.js',
        array(),
        defined( 'STARK_CHILD_VERSION' ) ? STARK_CHILD_VERSION : '27.0.0',
        array( 'in_footer' => true, 'strategy' => 'defer' )
    );
}
add_action( 'wp_enqueue_scripts', 'stark_enqueue_nav_script', 20 );

/**
 * Hide GeneratePress default header via a body class so CSS can target it.
 */
function stark_body_class_phase2( $classes ) {
    $classes[] = 'stark-phase-27';
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_phase2' );

/**
 * Remove GeneratePress default header render; Stark header replaces it.
 */
function stark_remove_generate_header() {
    remove_action( 'generate_header', 'generate_construct_header' );
}
add_action( 'wp', 'stark_remove_generate_header' );

/**
 * Render the Stark header at wp_body_open.
 */
function stark_render_header() {
    if ( is_admin() ) return;
    include get_stylesheet_directory() . '/templates/stark-header.php';
}
add_action( 'wp_body_open', 'stark_render_header', 5 );

/**
 * Fallback primary menu when none is assigned yet.
 */
function stark_fallback_primary_menu() {
    ob_start();
    ?>
    <ul class="stark-nav__menu">
        <li class="stark-nav__item">
            <a href="/work/" class="stark-nav__link"><span>Work</span></a>
        </li>
        <li class="stark-nav__item stark-nav__item--has-mega">
            <a href="/services/" class="stark-nav__link stark-nav__link--has-mega" aria-expanded="false" aria-haspopup="true">
                <span>Services</span>
                <svg class="stark-nav__chevron" width="10" height="6" viewBox="0 0 10 6" aria-hidden="true"><path d="M1 1l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <div class="stark-mega__panel"><div class="stark-mega__inner"><ul class="stark-mega__grid">
                <li class="stark-mega__item"><a href="/social-media-management/" class="stark-mega__link"><span class="stark-mega__title">Social Media Management</span><span class="stark-mega__desc">Content calendar, posting, reporting.</span></a></li>
                <li class="stark-mega__item"><a href="/paid-advertising/" class="stark-mega__link"><span class="stark-mega__title">Paid Advertising</span><span class="stark-mega__desc">Google, Meta, LinkedIn paid campaigns.</span></a></li>
                <li class="stark-mega__item"><a href="/web-design/" class="stark-mega__link"><span class="stark-mega__title">Web Design</span><span class="stark-mega__desc">Custom WordPress sites built to convert.</span></a></li>
                <li class="stark-mega__item"><a href="/seo/" class="stark-mega__link"><span class="stark-mega__title">SEO</span><span class="stark-mega__desc">Technical and local SEO that ranks.</span></a></li>
                <li class="stark-mega__item"><a href="/content-creation/" class="stark-mega__link"><span class="stark-mega__title">Content Creation</span><span class="stark-mega__desc">Photo, video, graphic, written.</span></a></li>
                <li class="stark-mega__item"><a href="/brand-strategy-identity/" class="stark-mega__link"><span class="stark-mega__title">Brand Strategy</span><span class="stark-mega__desc">Positioning and visual identity.</span></a></li>
                <li class="stark-mega__item"><a href="/audit-consulting/" class="stark-mega__link"><span class="stark-mega__title">Audit &amp; Consulting</span><span class="stark-mega__desc">Marketing audits for small to mid business.</span></a></li>
                <li class="stark-mega__cta">
                    <a href="/services/" class="stark-mega__cta-link">
                        <span class="stark-mega__cta-eyebrow">Not sure where to start?</span>
                        <span class="stark-mega__cta-title">See all services</span>
                        <span class="stark-mega__cta-arrow" aria-hidden="true">&rarr;</span>
                    </a>
                </li>
            </ul></div></div>
        </li>
        <li class="stark-nav__item"><a href="/blog/" class="stark-nav__link"><span>Blog</span></a></li>
        <li class="stark-nav__item"><a href="/podcast/" class="stark-nav__link"><span>Podcast</span></a></li>
        <li class="stark-nav__item"><a href="/contact/" class="stark-nav__link"><span>Contact</span></a></li>
    </ul>
    <?php
    return ob_get_clean();
}

/**
 * Fallback mobile menu when no menu assigned.
 */
function stark_fallback_mobile_menu() {
    ob_start();
    ?>
    <ul class="stark-mobile-nav__menu">
        <li><a href="/work/">Work</a></li>
        <li class="menu-item-has-children">
            <a href="/services/">Services</a>
            <ul class="sub-menu">
                <li><a href="/social-media-management/">Social Media Management</a></li>
                <li><a href="/paid-advertising/">Paid Advertising</a></li>
                <li><a href="/web-design/">Web Design</a></li>
                <li><a href="/seo/">SEO</a></li>
                <li><a href="/content-creation/">Content Creation</a></li>
                <li><a href="/brand-strategy-identity/">Brand Strategy</a></li>
                <li><a href="/audit-consulting/">Audit &amp; Consulting</a></li>
                <li><a href="/services/">See all services &rarr;</a></li>
            </ul>
        </li>
        <li><a href="/blog/">Blog</a></li>
        <li><a href="/podcast/">Podcast</a></li>
        <li><a href="/contact/">Contact</a></li>
    </ul>
    <?php
    return ob_get_clean();
}

/**
 * Stark 27 Footer — render on wp_footer, suppress GP default footer.
 * Added November 2 2026 (v27.0.2).
 */

function stark_remove_generate_footer() {
    remove_action( 'generate_footer', 'generate_construct_footer' );
    remove_action( 'generate_footer', 'generate_footer_bar' );
}
add_action( 'wp', 'stark_remove_generate_footer' );

function stark_render_footer() {
    if ( is_admin() ) return;
    include get_stylesheet_directory() . '/templates/stark-footer.php';
}
add_action( 'wp_footer', 'stark_render_footer', 1 );

/**
 * Stark 27 Homepage — hero body class, front page template, inline SVG helper.
 * Added November 2 2026 (v27.0.3).
 */

function stark_body_class_front_hero( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'stark-has-hero';
    }
    return $classes;
}
add_filter( 'body_class', 'stark_body_class_front_hero' );

function stark_front_page_template( $template ) {
    if ( is_front_page() ) {
        $stark_template = get_stylesheet_directory() . '/templates/stark-homepage.php';
        if ( file_exists( $stark_template ) ) {
            return $stark_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'stark_front_page_template', 99 );

/**
 * Return a theme SVG inline so it can inherit currentColor.
 */
function stark_inline_svg( $relative_path, $class = '' ) {
    $file = get_stylesheet_directory() . '/' . ltrim( $relative_path, '/' );
    if ( ! file_exists( $file ) ) return '';
    $svg = file_get_contents( $file );
    $attrs = ' aria-hidden="true" focusable="false"' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' );
    return preg_replace( '/<svg\b/', '<svg' . $attrs, $svg, 1 );
}

/**
 * Stark 27 Service pages — template, ACF location rule, context (v27.0.4).
 */
require_once get_stylesheet_directory() . '/inc/stark-services.php';
