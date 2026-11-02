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
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

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

    <section class="stark-services" aria-labelledby="stark-services-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">What we do</p>
                <h2 id="stark-services-title" class="stark-section-head__title">Services</h2>
            </header>
            <ul class="stark-services__grid">
                <?php foreach ( $services as $service ) : ?>
                    <li class="stark-service-card">
                        <span class="stark-service-card__icon" aria-hidden="true"></span>
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
