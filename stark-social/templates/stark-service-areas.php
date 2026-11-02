<?php
/**
 * Stark service areas hub (v27.0.6) — STUB
 * Lists the 5 markets with brief context. Full hub (per-market service
 * links, map, local proof) comes later.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$hero = array(
    'eyebrow'       => 'Service areas',
    'title'         => 'Service Areas',
    'thesis'        => 'Based in Santa Clarita, working with businesses across five markets.',
    'cta_primary'   => array( 'url' => '/contact/', 'title' => 'Start a project', 'target' => '' ),
    'cta_secondary' => null,
);

$cta = array(
    'final_cta' => array(
        'title'  => 'Not on the list?',
        'sub'    => "We work with businesses across Southern California. Let's talk.",
        'button' => array( 'url' => '/contact/', 'title' => 'Get in touch', 'target' => '' ),
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

<main id="main" class="stark-page stark-areas">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>

    <section class="stark-band stark-areas__markets" aria-labelledby="stark-areas-title">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <p class="stark-eyebrow">Where we work</p>
                <h2 id="stark-areas-title" class="stark-section-head__title">Our markets</h2>
            </header>
            <ul class="stark-who__grid">
                <?php foreach ( stark_get_markets() as $market ) : ?>
                    <li class="stark-who-card">
                        <div class="stark-who-card__text">
                            <h3 class="stark-who-card__title"><?php echo esc_html( $market['name'] ); ?></h3>
                            <p class="stark-who-card__desc"><?php echo esc_html( $market['desc'] ); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'cta', $cta ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
