<?php
/**
 * Stark homepage shell (v27.0.3)
 * Loaded for is_front_page() via template_include in functions.php.
 * Standalone document so full-bleed sections sit outside GP's containers.
 * Header renders on wp_body_open, footer on wp_footer.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$services = array(
    array( 'Social Media Management', '/social-media-management/', 'Content calendar, posting, reporting.' ),
    array( 'Paid Advertising', '/paid-advertising/', 'Google, Meta, LinkedIn paid campaigns.' ),
    array( 'Web Design', '/web-design/', 'Custom WordPress sites built to convert.' ),
    array( 'SEO', '/seo/', 'Technical and local SEO that ranks.' ),
    array( 'Content Creation', '/content-creation/', 'Photo, video, graphic, written.' ),
    array( 'Brand Strategy', '/brand-strategy-identity/', 'Positioning and visual identity.' ),
    array( 'Audit & Consulting', '/audit-consulting/', 'Marketing audits for small to mid business.' ),
);

// v27.3.0 — "Who we're for" copy from the live homepage.
$personas = array(
    array( 'Maybe your website feels outdated.', '' ),
    array( 'Maybe your social channels are inconsistent or quiet.', '' ),
    array( 'Maybe you’ve worked with agencies before and felt passed around, upsold, or underheard.', '' ),
);

$arrow = stark_inline_svg( 'img/who-for-diagonal-arrow-left-down.svg', 'stark-who__arrow-svg' );

// v27.0.20 — social proof, featured case study, FAQ (acf-json/group_stark_home.json).
$home_id    = (int) get_option( 'page_on_front' );
$home_draft = $home_id && 'approved' !== stark_field( 'copy_status', $home_id );
$home_stats = array_values( array_filter( (array) ( $home_id ? stark_field( 'stats', $home_id ) : array() ), function ( $row ) {
    return ! empty( $row['value'] ) && ! empty( $row['label'] );
} ) );

$featured_case = $home_id ? stark_field( 'featured_case_study', $home_id ) : null;
$featured_case = $featured_case ? get_post( $featured_case ) : ( function_exists( 'stark_latest_portfolio_item' ) ? stark_latest_portfolio_item() : null );

// v27.1.3 — process (same partial and look as the service pages).
$home_process = array_values( array_filter( (array) ( $home_id ? stark_field( 'process_steps', $home_id ) : array() ), function ( $row ) {
    return ! empty( $row['title'] );
} ) );

$home_faq = array_values( array_filter( (array) ( $home_id ? stark_field( 'faq_items', $home_id ) : array() ), function ( $row ) {
    return ! empty( $row['question'] ) && ! empty( $row['answer'] );
} ) );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( $home_draft && 'live' === stark_field( 'copy_status', $home_id ) ) : ?>
    <aside class="stark-draft-banner stark-draft-banner--live" aria-label="Review notice"><strong>[LIVE-SOURCED - needs Nathan voice review]</strong> <span class="stark-draft-banner__more">Sections below the hero use starksocial.com copy; the FAQ is still draft. Set <em>Copy status</em> to Approved when final.</span></aside>
<?php elseif ( $home_draft ) : ?>
    <aside class="stark-draft-banner" aria-label="Draft notice"><strong>[DRAFT - needs Nathan review]</strong> <span class="stark-draft-banner__more">Set <em>Copy status</em> to Approved on the Home page when the copy is final.</span></aside>
<?php endif; ?>

<main id="main" class="stark-home">

    <section class="stark-hero" aria-labelledby="stark-hero-title">
        <div class="stark-home__inner">
            <div class="stark-hero__content">
                <p class="stark-eyebrow stark-eyebrow--light">Southern California digital agency</p>
                <h1 id="stark-hero-title" class="stark-hero__title">Marketing that moves the needle.</h1>
                <p class="stark-hero__sub">A Southern California agency building honest, high-performing websites and campaigns for small-to-mid businesses.</p>
                <div class="stark-hero__ctas">
                    <a href="/contact/" class="stark-btn stark-btn--primary">Start a project</a>
                    <a href="/services/" class="stark-btn stark-btn--ghost">See our services</a>
                </div>
            </div>
        </div>
    </section>

    <?php if ( $home_stats ) : ?>
        <section class="stark-home-proof" aria-label="Stark Social at a glance">
            <div class="stark-home__inner">
                <dl class="stark-case-study__stats stark-case-study__stats--<?php echo esc_attr( count( $home_stats ) ); ?> stark-home-proof__stats">
                    <?php foreach ( $home_stats as $stat ) : ?>
                        <div class="stark-case-study__stat">
                            <dt><?php echo esc_html( $stat['label'] ); ?></dt>
                            <dd><?php echo esc_html( $stat['value'] ); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        </section>
    <?php endif; ?>

    <section class="stark-services" aria-labelledby="stark-services-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">What we do</p>
                <h2 id="stark-services-title" class="stark-section-head__title">Everything your brand needs to show up consistently online.</h2>
                <p class="stark-section-head__intro">If your website, social, and content don’t feel like they “match,” it’s hard for people to trust what they see—no matter how good your work is. We help you clean it up, tighten it up, and keep it moving, so your digital presence finally reflects the quality of what you do.</p>
            </header>
            <ul class="stark-services__grid">
                <?php foreach ( $services as $service ) : ?>
                    <li class="stark-service-card">
                        <?php $svc_icon = function_exists( 'stark_get_services' ) ? ( stark_get_services()[ trim( $service[1], '/' ) ]['icon'] ?? '' ) : ''; ?>
                        <span class="stark-service-card__icon stark-service-card__icon--glyph" aria-hidden="true"><?php echo $svc_icon ? stark_service_icon( $svc_icon ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                        <h3 class="stark-service-card__title"><a href="<?php echo esc_url( $service[1] ); ?>"><?php echo esc_html( $service[0] ); ?></a></h3>
                        <?php $svc_page = get_page_by_path( trim( $service[1], '/' ) ); ?>
                        <p class="stark-service-card__desc"><?php echo esc_html( ( $svc_page ? stark_field( 'card_description', $svc_page->ID ) : '' ) ?: $service[2] ); ?></p>
                        <span class="stark-service-card__more" aria-hidden="true">Learn more &rarr;</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <?php if ( $home_process ) :
        get_template_part( 'templates/parts/service', 'process', array(
            'process_eyebrow' => 'How We Work',
            'process_heading' => stark_field( 'process_heading', $home_id ) ?: 'How we work',
            'process_intro'   => 'We step in like an in-house marketing lead—setting direction, building structure, and keeping everything moving.',
            'process'         => $home_process,
        ) );
    endif; ?>

    <section class="stark-who" aria-labelledby="stark-who-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">Who We’re For</p>
                <h2 id="stark-who-title" class="stark-section-head__title">You’re doing good work—but your brand doesn’t fully show it yet.</h2>
                <p class="stark-section-head__intro">That’s where we come in. We partner with organizations that want clarity, consistency, and a digital presence they’re proud to share—without the agency fluff.</p>
            </header>
            <ul class="stark-who__grid">
                <?php foreach ( $personas as $persona ) : ?>
                    <li class="stark-who-card">
                        <div class="stark-who-card__text">
                            <h3 class="stark-who-card__title"><?php echo esc_html( $persona[0] ); ?></h3>
                            <?php if ( $persona[1] ) : ?><p class="stark-who-card__desc"><?php echo esc_html( $persona[1] ); ?></p><?php endif; ?>
                        </div>
                        <span class="stark-who-card__arrow" aria-hidden="true"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- theme-owned SVG ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="stark-band stark-why" aria-labelledby="stark-why-title">
        <div class="stark-home__inner stark-why__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">Why Stark</p>
                <h2 id="stark-why-title" class="stark-section-head__title">The Stark Difference</h2>
            </header>
            <div class="stark-why__copy">
                <p class="stark-why__lead">Most agencies sell deliverables. We step in like an in-house marketing lead—so your brand stays clear, consistent, and moving.</p>
                <p>You won’t get passed between account managers or junior teams. You work directly with the people setting direction and doing the work. We don’t chase trends or post just to stay “active.” Everything has a reason—and it fits into a bigger plan. We don’t disappear after launch. We stay involved, refine what’s working, and keep your marketing from slipping back into chaos.</p>
                <p class="stark-why__where">Based in Southern California (Los Angeles + Santa Clarita). Supporting businesses nationwide.</p>
            </div>
        </div>
    </section>

    <?php if ( $featured_case && function_exists( 'stark_portfolio_teaser' ) ) :
        get_template_part( 'templates/parts/service', 'case-study', array(
            'case_heading' => 'Featured work',
            'case_studies' => array( stark_portfolio_teaser( $featured_case ) ),
        ) );
    endif; ?>

    <?php if ( $home_faq ) :
        get_template_part( 'templates/parts/service', 'faq', array(
            'faq_heading' => stark_field( 'faq_heading', $home_id ) ?: 'Questions, answered',
            'faq'         => $home_faq,
        ) );
    endif; ?>

    <section class="stark-cta-band" aria-labelledby="stark-cta-title">
        <div class="stark-home__inner stark-cta-band__inner">
            <h2 id="stark-cta-title" class="stark-cta-band__title">Start a conversation</h2>
            <p class="stark-cta-band__sub">Tell us what you’re working on and we’ll help you think it through.</p>
            <a href="/contact/" class="stark-btn stark-btn--primary">Contact Us</a>
            <p class="stark-cta-band__note">No pressure. If we’re not a fit, we’ll tell you.</p>
        </div>
    </section>

</main>

<?php wp_footer(); ?>
</body>
</html>
