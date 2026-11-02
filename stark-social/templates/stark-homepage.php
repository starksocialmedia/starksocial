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

// Placeholder copy — Nathan writes real persona copy later.
$personas = array(
    array( 'SMB owners', 'You run the business. We run the marketing that grows it.' ),
    array( 'Nonprofits', 'Reach more donors and volunteers without stretching the budget.' ),
    array( 'Franchises', 'Brand consistency across locations, local reach in each market.' ),
    array( 'Service businesses', 'Turn searches and scrolls into booked calls and appointments.' ),
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

<?php if ( $home_draft ) : ?>
    <div class="stark-draft-banner" role="note"><strong>[DRAFT - needs Nathan review]</strong> <span class="stark-draft-banner__more">Set <em>Copy status</em> to Approved on the Home page when the copy is final.</span></div>
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
                <h2 id="stark-services-title" class="stark-section-head__title">Services</h2>
            </header>
            <ul class="stark-services__grid">
                <?php foreach ( $services as $service ) : ?>
                    <li class="stark-service-card">
                        <?php $svc_icon = function_exists( 'stark_get_services' ) ? ( stark_get_services()[ trim( $service[1], '/' ) ]['icon'] ?? '' ) : ''; ?>
                        <span class="stark-service-card__icon stark-service-card__icon--glyph" aria-hidden="true"><?php echo $svc_icon ? stark_service_icon( $svc_icon ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                        <h3 class="stark-service-card__title"><a href="<?php echo esc_url( $service[1] ); ?>"><?php echo esc_html( $service[0] ); ?></a></h3>
                        <p class="stark-service-card__desc"><?php echo esc_html( $service[2] ); ?></p>
                        <span class="stark-service-card__more" aria-hidden="true">Learn more &rarr;</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="stark-who" aria-labelledby="stark-who-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">Who we work with</p>
                <h2 id="stark-who-title" class="stark-section-head__title">Built for growing businesses</h2>
            </header>
            <ul class="stark-who__grid">
                <?php foreach ( $personas as $persona ) : ?>
                    <li class="stark-who-card">
                        <div class="stark-who-card__text">
                            <h3 class="stark-who-card__title"><?php echo esc_html( $persona[0] ); ?></h3>
                            <p class="stark-who-card__desc"><?php echo esc_html( $persona[1] ); ?></p>
                        </div>
                        <span class="stark-who-card__arrow" aria-hidden="true"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- theme-owned SVG ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
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
            <h2 id="stark-cta-title" class="stark-cta-band__title">Ready to grow?</h2>
            <p class="stark-cta-band__sub">Tell us where you want to be. We'll show you how we'd get there.</p>
            <a href="/contact/" class="stark-btn stark-btn--primary">Get in touch</a>
        </div>
    </section>

</main>

<?php wp_footer(); ?>
</body>
</html>
