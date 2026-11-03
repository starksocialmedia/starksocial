<?php
/**
 * Stark 404 (v27.0.10; popular pages, autofocus search, broken-link report v27.4.8)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$recent = get_posts( array( 'numberposts' => 3, 'suppress_filters' => false ) );

// Popular pages: the hubs plus whatever is newest on the blog and the podcast.
$popular = array(
    array( 'eyebrow' => 'Services', 'title' => 'What we do', 'url' => home_url( '/services/' ) ),
    array( 'eyebrow' => 'Work', 'title' => 'Case studies', 'url' => home_url( '/work/' ) ),
    array( 'eyebrow' => 'Blog', 'title' => 'All articles', 'url' => home_url( '/blog/' ) ),
);
if ( $recent ) {
    $popular[] = array( 'eyebrow' => 'Latest post', 'title' => get_the_title( $recent[0] ), 'url' => get_permalink( $recent[0] ) );
}
$episode = get_posts( array( 'post_type' => 'podcast', 'numberposts' => 1, 'suppress_filters' => false ) );
if ( $episode ) {
    $popular[] = array( 'eyebrow' => 'Latest episode', 'title' => get_the_title( $episode[0] ), 'url' => get_permalink( $episode[0] ) );
}
$requested = home_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- only used escaped, in a mailto body
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-404">
    <section class="stark-hero stark-hero--404" aria-labelledby="stark-404-title">
        <div class="stark-home__inner">
            <div class="stark-hero__content">
                <p class="stark-eyebrow stark-eyebrow--light">Error 404 · Page not found</p>
                <h1 id="stark-404-title" class="stark-hero__title">Lost in the hills?</h1>
                <p class="stark-hero__sub">Happens to the best of us. This page took a wrong turn somewhere off Sierra Highway. It moved, retired, or never existed. Search for it, or take one of the trails below.</p>
                <?php echo stark_search_form( 'light', 'stark-404-search', true ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper ?>
                <nav class="stark-404__links" aria-label="Popular trails">
                    <p class="stark-404__links-label">Popular trails</p>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                        <?php foreach ( stark_popular_links() as $link ) : ?>
                            <li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <p class="stark-404__report">
                    Followed a link here?
                    <a class="stark-404__report-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" data-page="<?php echo esc_attr( $requested ); ?>">Report a broken link</a>
                </p>
            </div>
        </div>
        <svg class="stark-404__hills" viewBox="0 0 1440 160" preserveAspectRatio="none" aria-hidden="true" focusable="false">
            <path d="M0 120 C 180 60 300 70 420 100 S 680 150 820 96 S 1100 30 1240 80 S 1400 120 1440 100 V160 H0 Z" fill="rgba(48,127,226,0.14)"/>
            <path d="M0 140 C 200 100 360 110 520 130 S 820 160 980 120 S 1260 90 1440 128 V160 H0 Z" fill="rgba(48,127,226,0.22)"/>
        </svg>
    </section>

    <section class="stark-band stark-404__popular" aria-labelledby="stark-404-popular-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">Popular pages</p>
                <h2 id="stark-404-popular-title" class="stark-section-head__title">Where most people head next</h2>
            </header>
            <ul class="stark-404__cards">
                <?php foreach ( $popular as $card ) : ?>
                    <li class="stark-404__card">
                        <p class="stark-404__card-eyebrow"><?php echo esc_html( $card['eyebrow'] ); ?></p>
                        <a class="stark-404__card-title" href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <?php if ( $recent ) : ?>
        <section class="stark-band stark-404__recent" aria-labelledby="stark-404-recent-title">
            <div class="stark-home__inner">
                <header class="stark-section-head">
                    <p class="stark-eyebrow">While you're here</p>
                    <h2 id="stark-404-recent-title" class="stark-section-head__title">Fresh from the blog</h2>
                </header>
                <div class="stark-post-grid">
                    <?php foreach ( $recent as $post_item ) get_template_part( 'templates/parts/blog', 'card', $post_item ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<script>
(function () {
    // Mailto built here so the address isn't in the page source; without JS the link goes to /contact/.
    var a = document.querySelector('.stark-404__report-link');
    if (!a) return;
    var to = ['nathan', 'starksocial.com'].join('@');
    var body = 'Broken page: ' + a.getAttribute('data-page') + '\n' + 'Linked from: ' + (document.referrer || '(typed or bookmarked)');
    a.href = 'mailto:' + to + '?subject=' + encodeURIComponent('Broken link on starksocial.com') + '&body=' + encodeURIComponent(body);
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
