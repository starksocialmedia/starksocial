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
    $page = get_page_by_path( $slug );
    $cards[ $slug ] = array(
        'slug'  => $slug,
        'name'  => $page ? get_the_title( $page ) : $service['name'],
        'pitch' => ( $page ? stark_field( 'card_description', $page->ID ) : '' ) ?: $service['desc'],
        'icon'  => $service['icon'],
        'url'   => '/' . $slug . '/',
    );
}
$featured = isset( $cards[ $featured_slug ] ) ? $cards[ $featured_slug ] : reset( $cards );
unset( $cards[ $featured['slug'] ] );
$featured_case = stark_service_portfolio_teasers( $featured['slug'], 1 );

$hero = array(
    'eyebrow'       => 'Services',
    'title'         => 'Marketing that earns its keep.',
    'thesis'        => 'Websites, search, social, ads and brand, planned as one system and run by the people who built it. Every service has a job, and every job gets measured.',
    'cta_primary'   => array( 'url' => '/contact/', 'title' => 'Start a conversation', 'target' => '' ),
    'cta_secondary' => null,
);

$cta = array(
    'final_cta' => array(
        'title'  => 'Not sure where to start?',
        'sub'    => "Book a quick call. We'll recommend the right mix for your goals, timeline and budget, and tell you honestly if we're not the fit.",
        'button' => array( 'url' => '/contact/', 'title' => "Let's talk", 'target' => '' ),
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

    <section class="stark-band stark-hub__services" aria-labelledby="stark-hub-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">What we do</p>
                <h2 id="stark-hub-title" class="stark-section-head__title">Seven services. One plan.</h2>
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
