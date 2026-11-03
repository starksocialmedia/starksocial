<?php
/**
 * Rank Math site-wide configuration for Phase 2 (v27.1.5). Idempotent — re-run
 * on staging and after cutover (URLs are taken from home_url()).
 *   wp eval-file tools/rankmath/configure.php
 * Per-page work that can't be automated: tools/rankmath/README.md.
 */
if ( ! class_exists( 'RankMath' ) ) WP_CLI::error( 'Rank Math is not active.' );

$home  = untrailingslashit( home_url() );
$brand = 'Stark Social Media Agency';

// Modules: Local SEO (Pro) for the LocalBusiness entity; keep the rest as they are.
$modules = (array) get_option( 'rank_math_modules', array() );
foreach ( array( 'local-seo', 'sitemap', 'rich-snippet' ) as $m ) {
    if ( ! in_array( $m, $modules, true ) ) $modules[] = $m;
}
update_option( 'rank_math_modules', array_values( $modules ) );

// Default share image — live's homepage image (1200×630). Register it once.
$og_file = 'stark-social-media-agency-homepage-1.webp';
$og_id   = (int) attachment_url_to_postid( wp_get_upload_dir()['baseurl'] . '/' . $og_file );
if ( ! $og_id && file_exists( wp_get_upload_dir()['basedir'] . '/' . $og_file ) ) {
    $og_id = wp_insert_attachment( array( 'post_title' => $brand, 'post_mime_type' => 'image/webp', 'post_status' => 'inherit' ), $og_file );
    update_post_meta( $og_id, '_wp_attachment_image_alt', $brand );
    wp_update_attachment_metadata( $og_id, array( 'width' => 1200, 'height' => 630, 'file' => $og_file ) );
}

/* -------------------------------------------------------------------------
   Titles & Meta
   ------------------------------------------------------------------------- */
$t = (array) get_option( 'rank-math-options-titles', array() );
$t = array_merge( $t, array(
    // Knowledge graph — company with Local SEO (service-area business).
    'knowledgegraph_type'        => 'company',
    'knowledgegraph_name'        => $brand,
    'website_name'               => $brand,
    'website_alternate_name'     => 'Stark Social',
    'local_business_type'        => 'ProfessionalService',
    'url'                        => $home,
    'email'                      => 'hello@starksocial.com',
    'phone'                      => '+1-661-513-3996',
    'phone_numbers'              => array( array( 'type' => 'customer support', 'number' => '+1-661-513-3996' ) ),
    // P.O. Box only — Google wants no street address for service-area businesses.
    'local_address'              => array( 'streetAddress' => '', 'addressLocality' => 'Valencia', 'addressRegion' => 'CA', 'postalCode' => '91385', 'addressCountry' => 'US' ),
    'local_address_format'       => '{locality}, {region} {postalcode}',
    'opening_hours'              => array(
        array( 'day' => 'Monday', 'time' => '09:00-17:00' ),
        array( 'day' => 'Tuesday', 'time' => '09:00-17:00' ),
        array( 'day' => 'Wednesday', 'time' => '09:00-17:00' ),
        array( 'day' => 'Thursday', 'time' => '09:00-17:00' ),
        array( 'day' => 'Friday', 'time' => '09:00-16:00' ),
    ),
    'opening_hours_format'       => 'off',
    // Social profiles (same as the footer + podcast links).
    'social_url_facebook'        => 'https://www.facebook.com/starksocial',
    'social_additional_profiles' => "https://www.instagram.com/starksocial\nhttps://www.linkedin.com/company/stark-social-media-agency\nhttps://www.youtube.com/c/Starksocial",
    // Share image defaults.
    'open_graph_image'           => $og_id ? wp_get_attachment_url( $og_id ) : '',
    'open_graph_image_id'        => $og_id,
    'twitter_card_type'          => 'summary_large_image',

    // Title templates.
    'title_separator'            => '|',
    'homepage_title'             => '%sitename% %sep% %sitedesc%',
    // Hero sub-line from the homepage (existing site copy).
    'homepage_description'       => 'A Southern California agency building honest, high-performing websites and campaigns for small-to-mid businesses.',
    'pt_page_title'              => '%title% %sep% %sitename%',
    'pt_page_description'        => '%excerpt%',
    'pt_post_title'              => '%title% %sep% Stark Social Media Agency Blog',
    'pt_post_description'        => '%excerpt%',
    'pt_portfolio_title'         => '%title% Case Study %sep% %sitename%',
    'pt_portfolio_description'   => '%excerpt%',
    'pt_portfolio_custom_robots' => 'off',
    'pt_podcast_title'           => '%title% %sep% Stark Social Media Agency Podcast',
    'pt_podcast_description'     => '%excerpt%',
    'pt_podcast_custom_robots'   => 'off',
    'pt_docs_title'              => '%title% %sep% Stark Social Knowledgebase',
    'pt_docs_description'        => '%excerpt%',
    'pt_portfolio_archive_title'       => 'Our Work %sep% %sitename%',
    'pt_portfolio_archive_description' => 'Websites, brands and marketing for businesses across Southern California. Real clients, real problems, and what we did about them.',
    'pt_podcast_archive_title'         => 'The Social Life Podcast %sep% %sitename%',
    'pt_podcast_archive_description'   => function_exists( 'stark_podcast_show' ) ? stark_podcast_show()['summary'] : '',
    'search_title'               => 'Search results for %search_query% %sep% %sitename%',
    '404_title'                  => 'Page not found %sep% %sitename%',
    // Default schema: posts are BlogPosting. Pages keep Rank Math's default
    // ("off" drops the whole Organization / WebSite / WebPage graph); the theme
    // removes the Article entity from pages (inc/stark-seo.php).
    'pt_post_default_rich_snippet'    => 'article',
    'pt_post_default_article_type'    => 'BlogPosting',
    'pt_page_default_rich_snippet'    => 'article',
    'pt_portfolio_default_rich_snippet' => 'article',
    'pt_portfolio_default_article_type' => 'Article',
    'noindex_search'             => 'on',
    'noindex_empty_taxonomies'   => 'on',
) );
update_option( 'rank-math-options-titles', $t );

/* -------------------------------------------------------------------------
   General — breadcrumbs, robots.txt
   ------------------------------------------------------------------------- */
$g = (array) get_option( 'rank-math-options-general', array() );
$g = array_merge( $g, array(
    'breadcrumbs'           => 'on',
    'breadcrumbs_separator' => '/',
    'breadcrumbs_home'      => 'on',
    'breadcrumbs_home_label'=> 'Home',
    'robots_txt_content'    => "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\nDisallow: /?s=\nDisallow: /search/\n\nSitemap: {$home}/sitemap_index.xml\n",
) );
update_option( 'rank-math-options-general', $g );

/* -------------------------------------------------------------------------
   Sitemap — posts, pages, portfolio, podcast, knowledgebase; legal pages out
   ------------------------------------------------------------------------- */
$legal_ids = array();
foreach ( array( 'privacy-policy', 'terms-of-service', 'cookie-policy', 'accessibility-statement' ) as $slug ) {
    $p = get_page_by_path( 'support/' . $slug );
    if ( $p ) $legal_ids[] = $p->ID;
}
$s = (array) get_option( 'rank-math-options-sitemap', array() );
$s = array_merge( $s, array(
    'pt_post_sitemap'          => 'on',
    'pt_page_sitemap'          => 'on',
    'pt_portfolio_sitemap'     => 'on',
    'pt_podcast_sitemap'       => 'on',
    'pt_docs_sitemap'          => 'on',
    'pt_attachment_sitemap'    => 'off',
    'tax_category_sitemap'     => 'on',
    'tax_post_tag_sitemap'     => 'off',
    'tax_doc_category_sitemap' => 'off',
    'authors_sitemap'          => 'on',
    'exclude_posts'            => implode( ',', $legal_ids ),
) );
update_option( 'rank-math-options-sitemap', $s );

// Legal pages: noindex in Rank Math too (the theme already filters robots), so tools agree.
foreach ( $legal_ids as $id ) update_post_meta( $id, 'rank_math_robots', array( 'noindex', 'follow' ) );

// Static front page and the services hub keep their lead copy in the template,
// so give them explicit Rank Math meta (same copy as on the page).
$front = (int) get_option( 'page_on_front' );
if ( $front ) {
    update_post_meta( $front, 'rank_math_title', '%sitename% %sep% %sitedesc%' );
    update_post_meta( $front, 'rank_math_description', $t['homepage_description'] );
}
$hub = get_page_by_path( 'services' );
if ( $hub ) {
    update_post_meta( $hub->ID, 'rank_math_title', 'Digital Marketing Services %sep% %sitename%' );
    update_post_meta( $hub->ID, 'rank_math_description', 'Websites, search, social, ads and brand, planned as one system and run by the people who built it.' );
}

if ( function_exists( 'rank_math' ) ) delete_transient( 'rank_math_sitemap_cache' );
flush_rewrite_rules( false );
WP_CLI::success( sprintf( 'Rank Math configured for %s — share image %d, %d legal pages excluded', $home, $og_id, count( $legal_ids ) ) );
