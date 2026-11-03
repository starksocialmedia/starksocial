<?php
/**
 * Stark services hub (v27.0.13) — /services/
 * Hero → featured service + 6 secondary cards → fit lists → CTA.
 * Card pitch comes from each service page's "Card description" field,
 * falling back to the registry description.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$featured_slug = apply_filters( 'stark_featured_service', 'web-design' );

$cards = array();
foreach ( stark_get_services() as $slug => $service ) {
    $svc_page = get_page_by_path( $slug );
    $cards[ $slug ] = array(
        'slug'  => $slug,
        'name'  => $svc_page ? get_the_title( $svc_page ) : $service['name'],
        'pitch' => ( $svc_page ? stark_field( 'card_description', $svc_page->ID ) : '' ) ?: $service['desc'],
        'icon'  => $service['icon'],
        'url'   => '/' . $slug . '/',
    );
}
$featured = isset( $cards[ $featured_slug ] ) ? $cards[ $featured_slug ] : reset( $cards );
unset( $cards[ $featured['slug'] ] );
$featured_case = stark_service_portfolio_teasers( $featured['slug'], 1 );

// v27.3.0 — copy from the live /services/ page.
$hero = array(
    'eyebrow'       => 'Services',
    'title'         => 'Marketing services built to grow your business — not just your presence.',
    'thesis'        => 'From social media and websites to content, ads, and SEO, we help brands turn attention into action.',
    'hero_note'     => 'Strategy-led. Fully in-house. Built to last.',
    'cta_primary'   => array( 'url' => '/contact/', 'title' => 'Start a conversation', 'target' => '' ),
    'cta_secondary' => array( 'url' => '#stark-hub-title', 'title' => 'Explore Services', 'target' => '' ),
);

$principles = array(
    'outcomes' => array(
        'eyebrow' => 'How we work',
        'heading' => '',
        'intro'   => '',
        'note'    => '',
        'items'   => array(
            array( 'title' => 'Clarity before execution.', 'description' => 'We start by understanding your goals, audience, and gaps — then build a plan that actually makes sense for your business.' ),
            array( 'title' => 'Consistency that builds trust.', 'description' => 'From social media to websites and content, we help brands show up clearly and consistently across every touchpoint.' ),
            array( 'title' => 'Growth you can measure.', 'description' => 'We focus on outcomes that matter — engagement, leads, conversions, and momentum — and refine as we go.' ),
        ),
    ),
);

$cta = array(
    'final_cta' => array(
        'title'  => 'Ready to turn attention into action?',
        'sub'    => 'Book a quick call and we’ll recommend the right mix of services for your goals, timeline, and budget.',
        'button' => array( 'url' => '/contact/', 'title' => "Let's Chat", 'target' => '' ),
        'note'   => 'No pressure. If we’re not a fit, we’ll tell you.',
    ),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-hub">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>
    <?php get_template_part( 'templates/parts/breadcrumbs' ); ?>
    <?php get_template_part( 'templates/parts/service', 'outcomes', $principles ); ?>

    <section class="stark-band stark-hub__services" aria-labelledby="stark-hub-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">Our Services</p>
                <h2 id="stark-hub-title" class="stark-section-head__title">Strategy-led marketing services designed to support growth at every stage.</h2>
            </header>

            <div class="stark-hub__grid">
                <article class="stark-hub-featured">
                    <div class="stark-hub-featured__body">
                        <span class="stark-hub-card__icon"><?php echo stark_service_icon( $featured['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                        <p class="stark-hub-featured__label">Where most clients start</p>
                        <h3 class="stark-hub-featured__title"><a href="<?php echo esc_url( $featured['url'] ); ?>"><?php echo esc_html( $featured['name'] ); ?></a></h3>
                        <p class="stark-hub-featured__pitch"><?php echo esc_html( $featured['pitch'] ); ?></p>
                        <span class="stark-hub-card__more" aria-hidden="true">Learn more &rarr;</span>
                    </div>
                    <?php if ( $featured_case ) : $fc = $featured_case[0]; ?>
                        <div class="stark-hub-featured__media">
                            <?php if ( ! empty( $fc['image']['ID'] ) ) echo wp_get_attachment_image( $fc['image']['ID'], 'large', false, array( 'loading' => 'lazy', 'alt' => '' ) ); ?>
                            <p class="stark-hub-featured__case"><span>Recent work</span> <?php echo esc_html( $fc['client'] ); ?></p>
                        </div>
                    <?php endif; ?>
                </article>

                <ul class="stark-hub__list">
                    <?php foreach ( $cards as $card ) : ?>
                        <li class="stark-hub-card">
                            <span class="stark-hub-card__icon"><?php echo stark_service_icon( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                            <h3 class="stark-hub-card__title"><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['name'] ); ?></a></h3>
                            <p class="stark-hub-card__pitch"><?php echo esc_html( $card['pitch'] ); ?></p>
                            <span class="stark-hub-card__more" aria-hidden="true">Learn more &rarr;</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <section class="stark-band stark-fit" aria-labelledby="stark-fit-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">Fit</p>
                <h2 id="stark-fit-title" class="stark-section-head__title">Who we work best with</h2>
            </header>
            <div class="stark-fit__grid">
                <div class="stark-fit__col stark-fit__col--yes">
                    <h3>A good fit if…</h3>
                    <ul>
                        <li>You want strategy before execution.</li>
                        <li>You care about consistency, not one-off campaigns.</li>
                        <li>You're looking for a long-term marketing partner.</li>
                        <li>You want measurable growth, not vanity metrics.</li>
                        <li>You're ready to invest in your brand's foundation.</li>
                    </ul>
                </div>
                <div class="stark-fit__col stark-fit__col--no">
                    <h3>Probably not a fit if…</h3>
                    <ul>
                        <li>You're looking for viral shortcuts or hacks.</li>
                        <li>You only need a single post or a quick task.</li>
                        <li>You want the cheapest option available.</li>
                        <li>You're not ready to commit to a clear strategy.</li>
                        <li>You prefer reactive marketing over planning.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
